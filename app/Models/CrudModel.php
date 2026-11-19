<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CrudModel
 *
 * Modelo base genérico. Todos los modelos CRUD deben extenderlo.
 *
 * Propiedades a sobreescribir en los hijos:
 *
 *   $table        Nombre de la tabla
 *   $primaryKey   PK de la tabla
 *   $hideFields   Columnas ocultas en la TABLA (además de la PK)
 *   $noEdit       Columnas ocultas en el modal de EDICIÓN
 *   $noInsert     Columnas ocultas en el modal de INSERCIÓN
 *   $foreignKeys  Definición de claves foráneas:
 *                 ['columna' => ['table' => 'tabla', 'display' => 'campo']]
 *
 * Relaciones N:N (pendiente de implementar):
 *   $manyToMany   Definición de relaciones N:N
 */
abstract class CrudModel extends Model
{
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = false;

    /** Columnas ocultas en la tabla */
    protected array $hideFields = [];

    /** Columnas ocultas en el modal de edición */
    protected array $noEdit = [];

    /** Columnas ocultas en el modal de inserción */
    protected array $noInsert = [];

    /**
     * Claves foráneas.
     * Formato:
     * [
     *   'idPadre' => ['table' => 'grupos', 'display' => 'nombre'],
     * ]
     */
    protected array $foreignKeys = [];

    /**
     * Relaciones N:M.
     * Cada entrada define una tabla pivote.
     *
     * Ejemplo:
     *   ['pivotTable' => 'relPG', 'display' => 'permiso', 'label' => 'Permisos']
     */
    protected array $manyToMany = [];

    public function getManyToMany(): array
        {
            return $this->manyToMany;
        }

    /**
     * Lee las FK de la tabla pivote desde information_schema
     * y devuelve qué columna apunta a la tabla local y cuál a la remota.
     *
     * Devuelve:
     *   [
     *     'localKey'    => 'idGrupo',
     *     'remoteKey'   => 'idPermiso',
     *     'remoteTable' => 'permisos',
     *   ]
     */
    public function getManyToManySchema(string $pivotTable): array
        {
            $sql = "
                SELECT
                    kcu.column_name,
                    ccu.table_name  AS foreign_table,
                    ccu.column_name AS foreign_column
                FROM information_schema.table_constraints AS tc
                JOIN information_schema.key_column_usage  AS kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema    = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                ON ccu.constraint_name = tc.constraint_name
                AND ccu.table_schema    = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                AND tc.table_schema    = 'public'
                AND tc.table_name      = ?
            ";

            $fks = $this->db->query($sql, [$pivotTable])->getResultArray();

            $localKey    = null;
            $remoteKey   = null;
            $remoteTable = null;

            foreach ($fks as $fk)
            {
                if ($fk['foreign_table'] === $this->table)
                {
                    $localKey = $fk['column_name'];
                }
                else
                {
                    $remoteKey   = $fk['column_name'];
                    $remoteTable = $fk['foreign_table'];
                }
            }

            return [
                'localKey'    => $localKey,
                'remoteKey'   => $remoteKey,
                'remoteTable' => $remoteTable,
            ];
        }

    /**
     * Devuelve los registros remotos YA asignados a un ID local.
     */
    public function getManyToManyItems(string $pivotTable, int|string $localId): array
        {
           $schema   = $this->getManyToManySchema($pivotTable);
            $mtm      = $this->getManyToManyConfig($pivotTable);
            $remotePk = $this->getPrimaryKeyOf($schema['remoteTable']);
            $pivotPk  = $this->getPrimaryKeyOf($pivotTable);

            $sql = "
                SELECT p.*, r.\"{$pivotPk}\" AS pivot_id
                FROM \"{$schema['remoteTable']}\" p
                JOIN \"{$pivotTable}\" r ON r.\"{$schema['remoteKey']}\" = p.\"{$remotePk}\"
                WHERE r.\"{$schema['localKey']}\" = ?
                ORDER BY p.\"{$mtm['display']}\"
            ";

            return $this->db->query($sql, [$localId])->getResultArray();
        }

    /**
     * Devuelve los registros remotos NO asignados aún (para el select de añadir).
     */
    public function getManyToManyAvailable(string $pivotTable, int|string $localId): array
        {
           $schema = $this->getManyToManySchema($pivotTable);
            $mtm    = $this->getManyToManyConfig($pivotTable);
            $pk     = $this->getPrimaryKeyOf($schema['remoteTable']);

            $sql = "
                SELECT *
                FROM \"{$schema['remoteTable']}\"
                WHERE \"{$pk}\" NOT IN (
                    SELECT \"{$schema['remoteKey']}\"
                    FROM \"{$pivotTable}\"
                    WHERE \"{$schema['localKey']}\" = ?
                )
                ORDER BY \"{$mtm['display']}\"
            ";

            return $this->db->query($sql, [$localId])->getResultArray();
        }

    /**
     * Añade una relación en la tabla pivote.
     */
    public function manyToManyAdd(string $pivotTable, int|string $localId, int|string $remoteId): bool
        {
            $schema = $this->getManyToManySchema($pivotTable);

            return $this->db->table($pivotTable)->insert([
                $schema['localKey']  => $localId,
                $schema['remoteKey'] => $remoteId,
            ]);
        }

    /**
     * Elimina una relación de la tabla pivote por su PK (pivot_id).
     */
    public function manyToManyRemove(string $pivotTable, int|string $pivotId): bool
        {
            $pk = $this->getPrimaryKeyOf($pivotTable);
            return $this->db->table($pivotTable)->where($pk, $pivotId)->delete();
        }

    // ── Helpers privados ────────────────────────────────────────────

    private function getManyToManyConfig(string $pivotTable): array
        {
            foreach ($this->manyToMany as $mtm)
            {
                if ($mtm['pivotTable'] === $pivotTable) return $mtm;
            }
            throw new \RuntimeException("ManyToMany config not found for pivot: {$pivotTable}");
        }

    private function getPrimaryKeyOf(string $table): string
        {
            $sql = "
                SELECT kcu.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema    = kcu.table_schema
                WHERE tc.constraint_type = 'PRIMARY KEY'
                AND tc.table_schema    = 'public'
                AND tc.table_name      = ?
                LIMIT 1
            ";
            $row = $this->db->query($sql, [$table])->getRowArray();
            return $row['column_name'] ?? 'id';
        }

    // ----------------------------------------------------------------
    // Getters de metadatos
    // ----------------------------------------------------------------

    public function getHideFields(): array  { return $this->hideFields; }
    public function getNoEdit(): array      { return $this->noEdit; }
    public function getNoInsert(): array    { return $this->noInsert; }
    public function getForeignKeys(): array { return $this->foreignKeys; }
    public function getPrimaryKey(): string { return $this->primaryKey; }

    // ----------------------------------------------------------------
    // Esquema desde information_schema (PostgreSQL, schema public)
    // ----------------------------------------------------------------

    /**
     * Devuelve el esquema completo de la tabla desde information_schema.
     *
     * Cada columna:
     * [
     *   'column_name'              => 'nombre',
     *   'data_type'                => 'character varying',
     *   'character_maximum_length' => 128,
     *   'numeric_precision'        => null,
     *   'is_nullable'              => 'YES'|'NO',
     *   'udt_name'                 => 'varchar',
     * ]
     */
    public function getSchema(): array
        {
            $sql = "
                SELECT
                    column_name,
                    data_type,
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable,
                    udt_name
                FROM information_schema.columns
                WHERE table_schema = 'public'
                AND table_name   = ?
                ORDER BY ordinal_position
            ";

            return $this->db->query($sql, [$this->table])->getResultArray();
        }

    /**
     * Devuelve solo las columnas visibles en la tabla (excluye PK y $hideFields).
     */
    public function getVisibleColumns(): array
        {
            $schema = $this->getSchema();
            $hide   = array_merge([$this->primaryKey], $this->hideFields);

            return array_filter($schema, fn($col) => ! in_array($col['column_name'], $hide, true));
        }

    /**
     * Devuelve las columnas para el modal de edición.
     */
    public function getEditColumns(): array
        {
            $schema = $this->getSchema();
            $hide   = array_merge([$this->primaryKey], $this->noEdit);

            return array_filter($schema, fn($col) => ! in_array($col['column_name'], $hide, true));
        }

    /**
     * Devuelve las columnas para el modal de inserción.
     */
    public function getInsertColumns(): array
        {
            $schema = $this->getSchema();
            $hide   = array_merge([$this->primaryKey], $this->noInsert);

            return array_filter($schema, fn($col) => ! in_array($col['column_name'], $hide, true));
        }

    // ----------------------------------------------------------------
    // Opciones para selects de FK
    // ----------------------------------------------------------------

    /**
     * Carga las opciones de una FK para mostrar en <select>.
     * Devuelve [['id' => ..., 'label' => ...], ...]
     *
     * Usa los mismos campos que se muestran en la tabla (display).
     */
    public function getForeignOptions(string $column): array
        {
            if (! isset($this->foreignKeys[$column]))
            {
                return [];
            }

            $fk      = $this->foreignKeys[$column];
            $table   = $fk['table'];
            $display = $fk['display'];

            // Buscar la PK de la tabla foránea en information_schema
            $pkSql = "
                SELECT kcu.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                ON tc.constraint_name = kcu.constraint_name
                AND tc.table_schema    = kcu.table_schema
                WHERE tc.constraint_type = 'PRIMARY KEY'
                AND tc.table_schema    = 'public'
                AND tc.table_name      = ?
                LIMIT 1
            ";

            $pkRow = $this->db->query($pkSql, [$table])->getRowArray();
            $pk    = $pkRow['column_name'] ?? 'id';

            $rows = $this->db
                        ->table($table)
                        ->select('"' . $pk . '", "' . $display . '"')
                        ->orderBy($display, 'ASC')
                        ->get()
                        ->getResultArray();

            return array_map(fn($r) => [
                'id'    => $r[$pk],
                'label' => $r[$display],
            ], $rows);
        }

    // ----------------------------------------------------------------
    // Listado con filtros, orden y paginación
    // ----------------------------------------------------------------

    /**
     * Devuelve registros paginados con JOIN para FKs, filtros y orden.
     *
     * @param array  $filters  ['columna' => 'valor', ...]
     * @param string $orderBy  Columna por la que ordenar
     * @param string $orderDir 'ASC' o 'DESC'
     * @param int    $perPage  Registros por página
     * @param int    $page     Página actual (1-based)
     * @return array ['data' => [...], 'total' => int]
     */
    public function getList(
        array  $filters  = [],
        string $orderBy  = '',
        string $orderDir = 'ASC',
        int    $perPage  = 25,
        int    $page     = 1
        ): array
        {
            $builder = $this->db->table($this->table . ' t');

            // SELECT: todas las columnas propias
            $select = ['t.*'];

            // JOINs para FKs y campos display
            foreach ($this->foreignKeys as $col => $fk)
            {
                $alias   = 'fk_' . strtolower($col);
                $display = $fk['display'];

                // Buscar PK de la tabla foránea
                $pkSql = "
                    SELECT kcu.column_name
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.key_column_usage kcu
                    ON tc.constraint_name = kcu.constraint_name
                    WHERE tc.constraint_type = 'PRIMARY KEY'
                    AND tc.table_schema    = 'public'
                    AND tc.table_name      = ?
                    LIMIT 1
                ";
                $pkRow   = $this->db->query($pkSql, [$fk['table']])->getRowArray();
                $fkPk    = $pkRow['column_name'] ?? 'id';

                $builder->join(
                    $fk['table'] . ' ' . $alias,
                    "t.\"{$col}\" = {$alias}.\"{$fkPk}\"",
                    'left'
                );

                $select[] = "{$alias}.\"{$display}\" AS __fk_" . strtolower($col);
            }

            $builder->select(implode(', ', $select));

            // Filtros (ILIKE para texto, = para numéricos)
            foreach ($filters as $col => $val)
            {
                if ($val === '' || $val === null)
                {
                    continue;
                }

                $schema = $this->getSchema();
                $colDef = array_values(array_filter($schema, fn($c) => $c['column_name'] === $col));
                $type   = $colDef[0]['data_type'] ?? 'character varying';

                if (str_contains($type, 'int') || str_contains($type, 'numeric') || str_contains($type, 'double'))
                {
                    $builder->where("t.\"{$col}\"", $val);
                }
                else
                {
                    $builder->like("t.\"{$col}\"", $val, 'both', null, true); // ILIKE
                }
            }

            // Orden
            $validDirs = ['ASC', 'DESC'];
            $orderDir  = in_array(strtoupper($orderDir), $validDirs, true) ? strtoupper($orderDir) : 'ASC';

            if ($orderBy !== '')
            {
                $builder->orderBy("t.\"{$orderBy}\"", $orderDir);
            }
            else
            {
                $builder->orderBy("t.\"{$this->primaryKey}\"", 'ASC');
            }

            // Total para paginación
            $total = $builder->countAllResults(false);

            // Paginación
            $offset = ($page - 1) * $perPage;
            $data   = $builder->limit($perPage, $offset)->get()->getResultArray();

            return ['data' => $data, 'total' => $total];
        }
    public function getPasswordFields(): array
        {
            return $this->passwordFields ?? [];
        }
}
