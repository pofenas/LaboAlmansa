<?php

namespace App\Controllers;

use App\Models\IncidenciaEquipoModelCrud;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Fer extends BaseController
{
    protected string $pageTitle = 'Reporte rápido de incidencias';

    public function index(int $idEquipo): void
        {
        helper(['form', 'url']);

        $equipo = $this->buscarEquipo($idEquipo);

        echo view('fer/form', [
            'equipo' => $equipo,
            'tipos'  => $this->tiposDelEquipo($idEquipo),
            'datos'  => [
                'fecha_incidencia' => date('Y-m-d'),
                'id_tie'           => '',
                'bloquea_equipo'   => '0',
                'incidencia'       => '',
            ],
            'errores' => [],
        ]);
        }

    public function guardar(int $idEquipo): ResponseInterface
        {
        helper(['form', 'url']);

        $equipo = $this->buscarEquipo($idEquipo);
        $tipos  = $this->tiposDelEquipo($idEquipo);

        // Solo se recogen los campos permitidos.
        // El equipo procede de la ruta, no del formulario.
        $datos = [
            'fecha_incidencia' => $this->request->getPost('fecha_incidencia'),
            'id_tie'           => $this->request->getPost('id_tie'),
            'bloquea_equipo'   => $this->request->getPost('bloquea_equipo') ?? '0',
            'incidencia'       => $this->request->getPost('incidencia'),
        ];

        $reglas = [
            'fecha_incidencia' => [
                'label' => 'Fecha de incidencia',
                'rules' => 'required|valid_date[Y-m-d]',
            ],
            'id_tie' => [
                'label' => 'Tipo de incidencia',
                'rules' => 'required|is_natural_no_zero',
            ],
            'bloquea_equipo' => [
                'label' => 'Bloquea el equipo',
                'rules' => 'required|in_list[0,1]',
            ],
            'incidencia' => [
                'label' => 'Descripción',
                'rules' => 'permit_empty|max_length[10000]',
            ],
        ];

        $errores = [];

        if (! $this->validateData($datos, $reglas))
        {
            $errores = $this->validator->getErrors();
        }
        else
        {
            $datos = $this->validator->getValidated();

            // No basta con que el tipo exista:
            // debe estar asignado al equipo indicado.
            $permitido = db_connect()
                ->table('r_tie_equipo')
                ->where('id_equipo', $idEquipo)
                ->where('id_tie', $datos['id_tie'])
                ->countAllResults() > 0;

            if (! $permitido)
            {
                $errores['id_tie'] =
                    'El tipo de incidencia no es aplicable a este equipo.';
            }
        }

        if ($errores === [])
        {
            $modelo = new IncidenciaEquipoModelCrud();

            $descripcion = trim((string) ($datos['incidencia'] ?? ''));

            $registro = [
                'fecha_incidencia' => $datos['fecha_incidencia'],
                'id_tie'           => (int) $datos['id_tie'],
                'id_equipo'        => $idEquipo,
                'incidencia'       => $descripcion !== '' ? $descripcion : null,
                'accion_tomada'    => null,
                'fecha_resolucion' => null,
                'bloquea_equipo'   => $datos['bloquea_equipo'] === '1'
                    ? 'true'
                    : 'false',
            ];

            if ($modelo->insert($registro) !== false)
            {
                return redirect()
                    ->to(site_url('fer/' . $idEquipo))
                    ->with(
                        'fer_exito',
                        'La incidencia se ha registrado correctamente.'
                    );
            }

            $errores = $modelo->errors();

            if ($errores === [])
            {
                $errores['_global'] =
                    'No se ha podido registrar la incidencia.';
            }
        }

        $this->response->setStatusCode(422);

        return $this->response
            ->setStatusCode(422)
            ->setBody(view('fer/form', [
                'equipo'  => $equipo,
                'tipos'   => $tipos,
                'datos'   => $datos,
                'errores' => $errores,
            ]));

        return $this->response;
        }

    private function buscarEquipo(int $idEquipo): array
        {
        $equipo = db_connect()
            ->table('equipos')
            ->where('id_equipo', $idEquipo)
            ->get()
            ->getRowArray();

        if (! $equipo)
        {
            throw PageNotFoundException::forPageNotFound(
                'El equipo indicado no existe.'
            );
        }

         return $equipo;
        }

    private function tiposDelEquipo(int $idEquipo): array
        {
        return db_connect()
            ->table('tie t')
            ->select('t.id_tie, t.tipo_incidencia')
            ->distinct()
            ->join('r_tie_equipo r', 'r.id_tie = t.id_tie')
            ->where('r.id_equipo', $idEquipo)
            ->orderBy('t.tipo_incidencia', 'ASC')
            ->get()
            ->getResultArray();
        }
}