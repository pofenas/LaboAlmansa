<?php

namespace App\Controllers;

use App\Models\IncidenciaEquipoModelCrud;

class IncidenciasEquipos extends CrudController
{
    protected string $modelClass = IncidenciaEquipoModelCrud::class;
    protected string $pageTitle  = 'Incidencias de equipos';
}