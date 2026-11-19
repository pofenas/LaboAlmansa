<?php

namespace App\Controllers;

use App\Models\ProveedorModelCrud;

class Proveedores extends CrudController
{
    protected string $modelClass = ProveedorModelCrud::class;
    protected string $pageTitle  = 'Proveedores';
}