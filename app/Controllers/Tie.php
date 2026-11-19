<?php

namespace App\Controllers;

use App\Models\TieModelCrud;

class Tie extends CrudController
{
    protected string $modelClass = TieModelCrud::class;
    protected string $pageTitle  = 'Tipos de incidencias por equipo';
}