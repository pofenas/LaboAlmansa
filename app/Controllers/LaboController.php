<?php

namespace App\Controllers;

class LaboController extends BaseController
{
    // Sin restricciones: cualquier usuario autenticado puede ver esto
    protected array  $allow     = [];
    protected array  $deny      = [];
    protected string $pageTitle = 'Prueba';

    public function index(): void
    {
        $this->render('demo/index');
    }
}