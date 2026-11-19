<?php

namespace App\Controllers;

use App\Models\EquipoModelCrud;

class Equipos extends CrudController
{
    protected string $modelClass = EquipoModelCrud::class;
    protected string $pageTitle  = 'Equipos';
}