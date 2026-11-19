<?php

namespace App\Controllers;
use App\Models\HospitalesModelCrud;

class Hospitales extends CrudController
{
    protected function crudConfig(): array
    {
        return [
            'table'      => 'hospitales',
            'primaryKey' => 'id_hospital',
            'defaultOrderBy'  => 'hospital',
            'defaultOrderDir' => 'ASC',
            'visibleColumns'  => ['hospital'],
            'editColumns'     => ['hospital'],
            'insertColumns'   => ['hospital'],
        ];
    }
    protected string $modelClass = HospitalesModelCrud::class;
}