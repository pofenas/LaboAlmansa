<?php

namespace App\Controllers;
use App\Models\PruebaModel;

use CodeIgniter\Controller;


class GuestController extends Controller
{
    protected $pruebaModel;

    public function initController(
            \CodeIgniter\HTTP\RequestInterface $request,
            \CodeIgniter\HTTP\ResponseInterface $response,
            \Psr\Log\LoggerInterface $logger
        )
         {
            parent::initController($request, $response, $logger);
            $this->pruebaModel = new \App\Models\PruebaModel();
        }

    public function index()
    {
        return view('guest');
    }

    public function buscar()
    {
        $q = strtolower($this->request->getGet('q'));

        if ($q === null || mb_strlen(trim($q)) < 3) {
            return $this->response->setJSON([]);
        }
        $patron = '%' . pg_escape_string(trim($q)) . '%';


        $resultados = $this->pruebaModel
            //->like('prueba', trim($q))
           ->where("unaccent(prueba) ILIKE '{$patron}'", null, false)
            ->orderBy('prueba', 'ASC')
            ->findAll(15);

        return $this->response->setJSON($resultados);
    }

    public function prueba($id = null)
    {
     $fila = $this->pruebaModel->find($id);

     if ($fila === null) {
         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
     }  

     $etiquetas = [
        'valor_referencia'          => 'Valores de referencia',
        'tubo'                      => 'Tubo',
        'unidades'                  => 'Unidades',
        'caracteristicas'           => 'Características',
        'significado_clinico'       => 'Significado clínico',
        'condiciones_preanaliticas' => 'Condiciones preanalíticas',
        'cuando_solicitar'          => 'Cuándo solicitar',
        'interferencias'            => 'Interferencias',
     ];

     return view('detalle', [
        'fila'      => $fila,
        'etiquetas' => $etiquetas,
     ]);
    }
}