<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla incidencias_materiales.
 * Campos: id, proveedor, fecha_incidencia, fecha_reclamacion, incidencia,
 *         producto, lote, fecha_caducidad, accion, resolucion,
 *         incidencia_pedido, observaciones, referencia, fecha, fecha_resolucion
 */
class IncidenciaMaterialModelCrud extends CrudModel
{
    protected $table      = 'incidencias_materiales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'proveedor',
        'fecha_incidencia',
        'fecha_reclamacion',
        'incidencia',
        'producto',
        'lote',
        'fecha_caducidad',
        'accion',
        'resolucion',
        'observaciones',
        'referencia',
        'fecha',
        'fecha_resolucion',
    ];

    protected $validationRules = [
    'proveedor'          => 'permit_empty|integer',
    'fecha_incidencia'   => 'permit_empty',
    'fecha_reclamacion'  => 'permit_empty',
    'incidencia'         => 'permit_empty',
    'producto'           => 'permit_empty|max_length[64]',
    'lote'               => 'permit_empty|max_length[64]',
    'fecha_caducidad'    => 'permit_empty',
    'accion'             => 'permit_empty',
    'resolucion'         => 'permit_empty',
    'observaciones'      => 'permit_empty',
    'referencia'         => 'permit_empty|max_length[16]',
    'fecha'              => 'permit_empty',
    'fecha_resolucion'   => 'permit_empty',
    ];

    
    
    /** Ocultar en tabla pero visible en modal (ninguno) */
    protected array $hideFields = [];

    /** Ocultar en modal de edición */
    protected array $noEdit = [];

    /** Ocultar en modal de inserción */
    protected array $noInsert = [];

    /**
     * FK: proveedor → proveedores.id, mostrar nombre.
     */
    protected array $foreignKeys = [
        'proveedor' => ['table' => 'proveedores', 'display' => 'nombre'],
    ];
}