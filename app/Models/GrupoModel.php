<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * GrupoModel
 *
 * Tabla: grupos
 * Campos: idGrupo, nombre, idPadre (nullable)
 *
 * Si idPadre no existe aún en la tabla, ejecutar:
 *   ALTER TABLE grupos ADD COLUMN "idPadre" integer DEFAULT NULL;
 */
class GrupoModel extends Model
{
    protected $table          = 'grupos';
    protected $primaryKey     = 'idGrupo';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    protected $allowedFields = ['nombre', 'idPadre'];

    protected $validationRules = [
        'nombre' => 'required|max_length[128]',
    ];

    // ----------------------------------------------------------------
    // Permisos
    // ----------------------------------------------------------------

    /**
     * Devuelve los permisos asignados DIRECTAMENTE a un grupo.
     * La recursión hacia el padre la gestiona Usuario::cargarPermisos().
     */
    public function getPermisos(int $idGrupo): array
        {
            return $this->db
                        ->table('relPG')
                        ->select('permisos.idPermiso, permisos.permiso')
                        ->join('permisos', 'permisos.idPermiso = relPG.idPermiso', 'inner')
                        ->where('relPG.idGrupo', $idGrupo)
                        ->get()
                        ->getResultArray();
        }

    // ----------------------------------------------------------------
    // Consultas auxiliares
    // ----------------------------------------------------------------

    /** Árbol completo de grupos ordenado por nombre. */
    public function getArbol(): array
        {
            return $this->select('idGrupo, nombre, idPadre')
                        ->orderBy('nombre', 'ASC')
                        ->findAll();
        }

    /** Hijos directos de un grupo padre. */
    public function getHijos(int $idPadre): array
        {
            return $this->where('idPadre', $idPadre)->findAll();
        }
}