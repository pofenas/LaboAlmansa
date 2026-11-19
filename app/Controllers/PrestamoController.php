<?php

namespace App\Controllers;

use App\Models\PrestamoModel;
use App\Models\HospitalModel;

class PrestamoController extends BaseController
{
    protected $prestamoModel;
    protected $hospitalModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ):void  {
        parent::initController($request, $response, $logger);
        $this->prestamoModel = new PrestamoModel();
        $this->hospitalModel = new HospitalModel();
    }

    public function index()
    {
        return view('prestamos/listado', [
            'sinDevolver' => $this->prestamoModel->sinDevolver(),
            'devueltos'   => $this->prestamoModel->devueltos(50),
            'hospitales'  => $this->hospitalModel->listado(),
            'almansaId'   => HospitalModel::ALMANSA_ID,
        ]);
    }

    public function hospitales()
    {
        return $this->response->setJSON($this->hospitalModel->listado());
    }

    public function guardar()
    {
        $id = (int) $this->request->getPost('id_prestamo');

        $origen  = (int) $this->request->getPost('origen');
        $destino = (int) $this->request->getPost('destino');

        // Validación: exactamente uno de los dos debe ser Almansa.
        $origenAlmansa  = $origen  === HospitalModel::ALMANSA_ID;
        $destinoAlmansa = $destino === HospitalModel::ALMANSA_ID;

        if ($origenAlmansa === $destinoAlmansa) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['error' => 'Uno de los dos hospitales debe ser Almansa y el otro no.']);
        }

        if ($origen <= 0 || $destino <= 0) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(['error' => 'Debes seleccionar los dos hospitales.']);
        }

        $datos = [
            'origen'         => $origen,
            'destino'        => $destino,
            'articulo'       => trim((string) $this->request->getPost('articulo')),
            'unidades'       => (int) $this->request->getPost('unidades'),
            'fecha_prestamo' => $this->request->getPost('fecha_prestamo') ?: null,
            'notas'          => trim((string) $this->request->getPost('notas')),
        ];

        if ($datos['articulo'] === '') {
            return $this->response->setStatusCode(422)
                ->setJSON(['error' => 'El artículo no puede estar vacío.']);
        }

        if ($datos['unidades'] < 1) {
            return $this->response->setStatusCode(422)
                ->setJSON(['error' => 'Las unidades deben ser un número positivo.']);
        }

        if ($id > 0) {
            $this->prestamoModel->update($id, $datos);
        } else {
            $datos['id_user_p'] = $this->usuario->idUser;
            $this->prestamoModel->insert($datos);
        }

        return $this->response->setJSON(['ok' => true]);
    }

    public function eliminar($id)
    {
        $this->prestamoModel->delete((int) $id);
        return $this->response->setJSON(['ok' => true]);
    }

    public function devolver($id)
    {
        $this->prestamoModel->update((int) $id, [
            'fecha_dev' => date('Y-m-d'),
            'id_user_d' => $this->usuario->idUser,
        ]);

        return $this->response->setJSON(['ok' => true]);
    }
    public function anularDevolucion($id)
    {
        $this->prestamoModel->update((int) $id, [
            'fecha_dev' => null,
            'id_user_d' => null,
        ]);

     return $this->response->setJSON(['ok' => true]);
    }   
}
