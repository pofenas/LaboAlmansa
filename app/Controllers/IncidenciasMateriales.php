<?php

namespace App\Controllers;

use App\Models\IncidenciaMaterialModelCrud;

class IncidenciasMateriales extends CrudController
{
    protected string $modelClass = IncidenciaMaterialModelCrud::class;
    protected string $pageTitle  = 'Incidencias de materiales';
}