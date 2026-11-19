<?php

namespace App\Controllers;

class Grupos extends CrudController
{
    protected string $modelClass = \App\Models\GrupoModelCrud::class;
    protected string $pageTitle  = 'Grupos de usuarios';
    protected array  $allow      = [];
    protected array  $deny       = [];
}
