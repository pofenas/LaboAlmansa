<?php

namespace App\Models;


/**
 * Modelo CRUD para la tabla hospitales.
 * Campos: id_hospital, hospital
 */
class HospitalesModelCrud extends CrudModel
{
    protected $table      = 'hospitales';
    protected $primaryKey = 'id_hospital';

    protected $allowedFields = ['hospital'];

    protected $validationRules = [
        'hospital' => 'required|max_length[128]',
    ];

    /** Ocultar en tabla pero visible en modal (ninguno aquí) */
    protected array $hideFields = [];

    /** Ocultar en modal de edición */
    protected array $noEdit = [];

    /** Ocultar en modal de inserción */
    protected array $noInsert = [];
}