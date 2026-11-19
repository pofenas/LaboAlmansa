<?php

namespace App\Models;

/**
 * GrupoModel
 *
 * Tabla: grupos
 * Campos: idGrupo, nombre, idPadre (nullable)
 *
 * Si idPadre no existe aún:
 *   ALTER TABLE grupos ADD COLUMN "idPadre" integer DEFAULT NULL;
 */
class GrupoModelCrud extends CrudModel
{
    protected $table      = 'grupos';
    protected $primaryKey = 'idGrupo';

    protected $allowedFields = ['nombre', 'idPadre'];

    protected $validationRules = [
        'nombre' => 'required|max_length[128]',
    ];

    /** Ocultar en tabla */
    protected array $hideFields = [];

    /** Ocultar en modal de edición */
    protected array $noEdit = [];

    /** Ocultar en modal de inserción */
    protected array $noInsert = [];

    /**
     * FK: idPadre apunta a la propia tabla grupos, campo nombre.
     * Permite mostrar el nombre del grupo padre en la tabla,
     * y un <select> con los grupos disponibles en los modales.
     */
    protected array $foreignKeys = [
        'idPadre' => ['table' => 'grupos', 'display' => 'nombre'],
    ];
    protected array $manyToMany = [
        [
            'pivotTable' => 'relPG',
            'display'    => 'permiso',
            'label'      => 'Permisos',
        ],
    ];

    // ----------------------------------------------------------------
    // Métodos propios (además de los heredados de CrudModel)
    // ----------------------------------------------------------------

    /**
     * Devuelve los permisos asignados directamente a un grupo.
     * (Usado por Usuario::cargarPermisos())
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

    /** Hijos directos de un grupo padre. */
    public function getHijos(int $idPadre): array
    {
        return $this->where('idPadre', $idPadre)->findAll();
    }
}
