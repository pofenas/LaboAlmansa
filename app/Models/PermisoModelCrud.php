<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla permisos.
 * Campos: idPermiso, permiso
 */
class PermisoModelCrud extends CrudModel
{
    protected $table      = 'permisos';
    protected $primaryKey = 'idPermiso';

    protected $allowedFields = ['permiso'];

    protected $validationRules = [
        'permiso' => 'required|max_length[20]',
    ];

    protected array $hideFields  = [];
    protected array $noEdit      = [];
    protected array $noInsert    = [];
    protected array $foreignKeys = [];
}