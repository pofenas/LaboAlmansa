<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla equipos.
 *
 * Campos:
 * - id_equipo: clave primaria autogenerada.
 * - nombre: nombre del equipo.
 * - id_proveedor: proveedor del equipo.
 */
class EquipoModelCrud extends CrudModel
{
    protected $table            = 'equipos';
    protected $primaryKey       = 'id_equipo';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nombre',
        'id_proveedor',
    ];

    protected $validationRules = [
        'nombre'       => 'required|max_length[128]',
        'id_proveedor' => 'required|integer|is_not_unique[proveedores.id]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre del equipo es obligatorio.',
            'max_length' => 'El nombre no puede superar los 128 caracteres.',
        ],
        'id_proveedor' => [
            'required'      => 'Debes seleccionar un proveedor.',
            'integer'       => 'El proveedor debe ser un identificador válido.',
            'is_not_unique' => 'El proveedor seleccionado no existe.',
        ],
    ];

    /** Campos ocultos en la tabla. */
    protected array $hideFields = [];

    /** Campos ocultos en el modal de edición. */
    protected array $noEdit = [
        'id_equipo',
    ];

    /** Campos ocultos en el modal de inserción. */
    protected array $noInsert = [
        'id_equipo',
    ];

    /** Proveedor: mostrar su nombre en lugar del identificador. */
    protected array $foreignKeys = [
        'id_proveedor' => [
            'table'   => 'proveedores',
            'display' => 'nombre',
        ],
    ];
}