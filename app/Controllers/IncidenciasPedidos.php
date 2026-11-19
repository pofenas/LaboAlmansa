<?php

namespace App\Controllers;

use App\Models\IncidenciaPedidoModelCrud;

class IncidenciasPedidos extends CrudController
{
    protected string $modelClass = IncidenciaPedidoModelCrud::class;
    protected string $pageTitle  = 'Incidencias de pedidos';
}