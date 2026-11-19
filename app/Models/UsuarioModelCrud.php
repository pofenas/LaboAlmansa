<?php

namespace App\Models;

/**
 * Modelo CRUD para la tabla usuarios.
 * Campos: idUser, alias, nombre, telefono, empresa, idGrupo, password
 */
class UsuarioModelCrud extends CrudModel
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'idUser';

    protected $allowedFields = ['alias', 'nombre', 'telefono', 'empresa', 'idGrupo', 'password'];

    protected $validationRules = [
        'alias'    => 'required|max_length[16]',
        'nombre'   => 'max_length[256]',
        'telefono' => 'max_length[12]',
        'empresa'  => 'max_length[128]',
        'idGrupo'  => 'required|integer',
        'password' => 'required|max_length[32]',
    ];

    /** Ocultar en tabla pero visible en modal */
    protected array $hideFields = ['password'];

    /** Ocultar en modal de edición */
    protected array $noEdit = [];

    /** Ocultar en modal de inserción */
    protected array $noInsert = [];

    /**
     * FK: idGrupo apunta a grupos, campo nombre.
     */
    protected array $foreignKeys = [
        'idGrupo' => ['table' => 'grupos', 'display' => 'nombre'],
    ];
    /** Campos ocultados y codificados con MD5 */
    protected array $passwordFields = ['password'];
}