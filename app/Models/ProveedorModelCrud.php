<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla proveedores.
 * Campos: id, nombre
 */
class ProveedorModelCrud extends CrudModel
{
    protected $table      = 'proveedores';
    protected $primaryKey = 'id';

    protected $allowedFields = ['nombre'];

    protected $validationRules = [
        'nombre' => 'required|max_length[32]',
    ];

    /** Ocultar en tabla pero visible en modal (ninguno aquí) */
    protected array $hideFields = [];

    /** Ocultar en modal de edición */
    protected array $noEdit = [];

    /** Ocultar en modal de inserción */
    protected array $noInsert = [];
}