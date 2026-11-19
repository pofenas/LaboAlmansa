<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PermisoModel
 *
 * Tabla: permisos
 * Campos: idPermiso, permiso (char 10)
 */
class PermisoModel extends Model
{
    protected $table          = 'permisos';
    protected $primaryKey     = 'idPermiso';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = ['permiso'];

    protected $validationRules = [
        'permiso' => 'required|max_length[10]',
    ];

    // ----------------------------------------------------------------
    // Gestión de relación grupo↔permiso
    // ----------------------------------------------------------------

    /** Asigna un permiso a un grupo (inserta en relPG, evita duplicados). */
    public function asignarAGrupo(int $idGrupo, int $idPermiso): bool
        {
            $existe = $this->db->table('relPG')
                            ->where('idGrupo',   $idGrupo)
                            ->where('idPermiso', $idPermiso)
                            ->countAllResults();

            if ($existe > 0)
            {
                return true;
            }

            return (bool) $this->db->table('relPG')
                                ->insert(['idGrupo' => $idGrupo, 'idPermiso' => $idPermiso]);
        }

    /** Quita un permiso de un grupo. */
    public function quitarDeGrupo(int $idGrupo, int $idPermiso): bool
        {
            return (bool) $this->db->table('relPG')
                                ->where('idGrupo',   $idGrupo)
                                ->where('idPermiso', $idPermiso)
                                ->delete();
        }

    /** Permisos de un grupo para gestión en UI (sin recursión). */
    public function getByGrupo(int $idGrupo): array
        {
            return $this->db
                        ->table('relPG')
                        ->select('permisos.*')
                        ->join('permisos', 'permisos.idPermiso = relPG.idPermiso', 'inner')
                        ->where('relPG.idGrupo', $idGrupo)
                        ->get()
                        ->getResultArray();
        }
}