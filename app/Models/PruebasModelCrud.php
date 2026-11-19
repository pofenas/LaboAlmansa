<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla permisos.
 * Campos: idPermiso, permiso
 */
class PruebasModelCrud extends CrudModel
{
    protected $table      = 'pruebas';
    protected $primaryKey = 'id_prueba';

    protected $allowedFields = ['prueba', 'valor_referencia', 'unidades', 'caracteristicas', 'significado_clinico',
                                'condiciones_preanaliticas', 'cuando_solicitar', 'quien_solicita', 'interferencias'];

    protected $validationRules = [
        'prueba' => 'required|max_length[128]',
    ];

    protected array $hideFields  = [];
    protected array $noEdit      = [];
    protected array $noInsert    = [];
    protected array $foreignKeys = [];
}