<?php
 
namespace App\Models;

use CodeIgniter\Model;

/**
 * UsuarioModel
 *
 * Tabla: usuarios
 * Campos: idUser, alias, nombre, telefono, empresa, idGrupo, password
 *
 * password se almacena como MD5 char(32) según el esquema actual.
 */
class UsuarioModel extends Model
{
    protected $table          = 'usuarios';
    protected $primaryKey     = 'idUser';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = [
        'alias', 'nombre', 'telefono', 'empresa', 'idGrupo', 'password',
    ];

    protected $validationRules = [
        'alias'    => 'required|max_length[16]',
        'nombre'   => 'required|max_length[256]',
        'idGrupo'  => 'required|integer',
        'password' => 'required|max_length[32]',
    ];

    // ----------------------------------------------------------------
    // Autenticación
    // ----------------------------------------------------------------

    /**
     * Valida alias + contraseña y devuelve la fila del usuario o null.
     *
     * @param string $alias
     * @param string $passwordPlano  Contraseña en texto plano
     */
    public function validarCredenciales(string $alias, string $passwordPlano): ?array
        {
            $hash = md5($passwordPlano);

            $row = $this->where('alias', $alias)
                        ->where('password', $hash)
                        ->first();
            return $row ?: null;
        }

    // ----------------------------------------------------------------
    // Consultas auxiliares
    // ----------------------------------------------------------------

    /** Devuelve usuarios con el nombre de su grupo. */
    public function getUsuariosConGrupo(): array
        {
            return $this->select('usuarios.*, grupos.nombre AS nombreGrupo')
                        ->join('grupos', 'grupos.idGrupo = usuarios.idGrupo', 'left')
                        ->findAll();
        }
}