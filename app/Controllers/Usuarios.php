<?php

namespace App\Controllers;

use App\Models\UsuarioModelCrud;

class Usuarios extends CrudController
{
    protected string $modelClass = UsuarioModelCrud::class;
    protected string $pageTitle  = 'Usuarios';
}