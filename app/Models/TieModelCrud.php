<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla tie.
 * Tipos de incidencias por equipo.
 *
 * Relación N:N:
 * tie → r_tie_equipo → equipos
 */
class TieModelCrud extends CrudModel
{
    protected $table            = 'tie';
    protected $primaryKey       = 'id_tie';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'tipo_incidencia',
    ];

    protected $validationRules = [
        'tipo_incidencia' => 'required',
    ];

    protected $validationMessages = [
        'tipo_incidencia' => [
            'required' => 'Debes indicar el tipo de incidencia.',
        ],
    ];

    /** Columnas ocultas en la tabla, además de la PK. */
    protected array $hideFields = [];

    /** La PK ya la excluye automáticamente CrudModel. */
    protected array $noEdit = [];

    /** La PK ya la excluye automáticamente CrudModel. */
    protected array $noInsert = [];

    /** La relación con equipos se gestiona mediante la tabla pivote. */
    protected array $foreignKeys = [];

    /**
     * Equipos en los que es aplicable cada tipo de incidencia.
     */
    protected array $manyToMany = [
        [
            'pivotTable' => 'r_tie_equipo',
            'display'    => 'nombre',
            'label'      => 'Equipos aplicables',
        ],
    ];
}