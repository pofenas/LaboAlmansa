<?php

namespace App\Controllers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Listado de equipos con sus códigos QR para el reporte rápido.
 */
class FerEquipos extends BaseController
{
    protected string $pageTitle = 'Códigos QR de equipos';

    public function index(): void
    {
        helper('url');

        $equipos = db_connect()
            ->table('equipos')
            ->select('id_equipo, nombre')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();

        $writer = new PngWriter();

        foreach ($equipos as &$equipo)
        {
            $url = site_url(
                'fer-equipos/' . (int) $equipo['id_equipo']
            );

            $qrCode = new QrCode(
                data: $url,
                size: 300,
                margin: 20
            );

            $resultado = $writer->write($qrCode);

            $equipo['fer_url'] = $url;
            $equipo['qr_uri']  = $resultado->getDataUri();
        }

        unset($equipo);

        $this->render('fer/equipos', [
            'equipos' => $equipos,
        ]);
    }
}