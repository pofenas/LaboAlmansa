<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla incidencias_equipos.
 */
class IncidenciaEquipoModelCrud extends CrudModel
{
    protected $table            = 'incidencias_equipos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'fecha_incidencia',
        'id_tie',
        'id_equipo',
        'incidencia',
        'accion_tomada',
        'fecha_resolucion',
        'bloquea_equipo',
    ];

    protected $validationRules = [
        'fecha_incidencia' => 'permit_empty|valid_date[Y-m-d]',
        'id_tie'           => 'required|integer|is_not_unique[tie.id_tie]',
        'id_equipo'        => 'required|integer|is_not_unique[equipos.id_equipo]',
        'incidencia'       => 'permit_empty',
        'accion_tomada'    => 'permit_empty',
        'fecha_resolucion' => 'permit_empty|valid_date[Y-m-d]',
        'bloquea_equipo'   => 'required|in_list[true,false,1,0,t,f]',
    ];

    protected $validationMessages = [
        'fecha_incidencia' => [
            'valid_date' => 'La fecha de incidencia no es válida.',
        ],
        'id_tie' => [
            'required'      => 'Debes seleccionar un tipo de incidencia.',
            'integer'       => 'El tipo de incidencia no es válido.',
            'is_not_unique' => 'El tipo de incidencia seleccionado no existe.',
        ],
        'id_equipo' => [
            'required'      => 'Debes seleccionar un equipo.',
            'integer'       => 'El equipo no es válido.',
            'is_not_unique' => 'El equipo seleccionado no existe.',
        ],
        'fecha_resolucion' => [
            'valid_date' => 'La fecha de resolución no es válida.',
        ],
        'bloquea_equipo' => [
            'required' => 'Debes indicar si la incidencia bloquea el equipo.',
            'in_list'  => 'El valor de bloqueo del equipo no es válido.',
        ],
    ];

    /** Columnas ocultas en el listado, además de la PK. */
    protected array $hideFields = [];

    /** La PK ya se excluye automáticamente. */
    protected array $noEdit = [];

    /** La PK ya se excluye automáticamente. */
    protected array $noInsert = [];

    protected array $foreignKeys = [
        'id_tie' => [
            'table'   => 'tie',
            'display' => 'tipo_incidencia',
        ],
        'id_equipo' => [
            'table'   => 'equipos',
            'display' => 'nombre',
        ],
    ];
}