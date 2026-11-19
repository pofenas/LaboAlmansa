<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla incidencias_pedidos.
 * Campos: id, fecha_pedido, referencia, descripcion_producto,
 *         proveedor, descripcion_incidencia, fecha_reclamacion
 */
class IncidenciaPedidoModelCrud extends CrudModel
{
    protected $table      = 'incidencias_pedidos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'fecha_pedido',
        'referencia',
        'descripcion_producto',
        'proveedor',
        'descripcion_incidencia',
        'fecha_reclamacion',
    ];

     protected $validationRules = [
        'fecha_pedido'            => 'permit_empty', // <-- Sin valid_date
        'referencia'              => 'permit_empty|max_length[16]',
        'descripcion_producto'    => 'permit_empty|max_length[64]',
        'proveedor'               => 'permit_empty|integer',
        'descripcion_incidencia'  => 'permit_empty',
        'fecha_reclamacion'       => 'permit_empty', // <-- Sin valid_date
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