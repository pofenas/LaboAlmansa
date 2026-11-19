<?php

namespace App\Controllers;
use App\Models\PruebasModelCrud;

class Pruebas extends CrudController
{
    protected function crudConfig(): array
    {
        return [
            'table'      => 'pruebas',
            'primaryKey' => 'id_prueba',
            'defaultOrderBy'  => 'prueba',
            'defaultOrderDir' => 'ASC',
            'visibleColumns'  => ['prueba','valor_referencia','unidades','caracteristicas',
                                  'significado_clinico','condiciones_preanaliticas','cuando_solicitar',
                                  'quien_solicita','interferencias'],
            'editColumns'     => ['prueba','valor_referencia','unidades','caracteristicas',
                                  'significado_clinico','condiciones_preanaliticas','cuando_solicitar',
                                  'quien_solicita','interferencias'],
            'insertColumns'   => ['prueba','valor_referencia','unidades','caracteristicas',
                                  'significado_clinico','condiciones_preanaliticas','cuando_solicitar',
                                  'quien_solicita','interferencias'],
        ];
    }
    protected string $modelClass = PruebasModelCrud::class;
}