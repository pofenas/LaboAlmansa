<?php

namespace App\Controllers;

use App\Models\PermisoModelCrud;

class Permisos extends CrudController
{
    protected string $modelClass = PermisoModelCrud::class;
    protected string $pageTitle  = 'Permisos';
}