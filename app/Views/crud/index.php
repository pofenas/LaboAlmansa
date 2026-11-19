<?php
/**
 * Vista CRUD genérica
 *
 * Variables disponibles:
 *   $rows, $total, $page, $pages, $perPage
 *   $orderBy, $orderDir, $filters
 *   $visibleColumns, $foreignKeys, $primaryKey
 *   $fkOptions, $editColumns, $insertColumns
 *   $baseUrl
 */

// Helper: genera URL conservando todos los params GET y sobreescribiendo los indicados
function crudUrl(array $params): string
{
    $current = $_GET;
    foreach ($params as $k => $v)
    {
        $current[$k] = $v;
    }
    return '?' . http_build_query($current);
}

// Helper: icono de orden en cabecera
function sortIcon(string $col, string $orderBy, string $orderDir): string
{
    if ($orderBy !== $col) return '<span class="sort-icon">⇅</span>';
    return $orderDir === 'ASC'
        ? '<span class="sort-icon active">↑</span>'
        : '<span class="sort-icon active">↓</span>';
}
?>

<div class="crud-wrapper" x-data="crudTable()" x-init="init()">
   

    <!-- ── Barra superior ───────────────────────────────────── -->
    <div class="crud-toolbar">
        <div class="crud-toolbar__info">
            Total: <strong><?= $total ?></strong> registros
            &nbsp;|&nbsp;
            Página <strong><?= $page ?></strong> de <strong><?= $pages ?></strong>
        </div>

        <div class="crud-toolbar__actions">
            <!-- Selector registros por página -->
            <label class="crud-label">
                Mostrar
                <select class="crud-select" onchange="location='<?= crudUrl(['perPage' => '']) ?>' + this.value">
                    <?php foreach ([25, 50, 100] as $n): ?>
                        <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach ?>
                </select>
                por página
            </label>

            <!-- Botón nuevo registro -->
            <button class="btn btn-primary"
                    @click="openInsert()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nuevo
            </button>
        </div>
    </div>

 <!-- ── Tabla ────────────────────────────────────────────── -->
    <div class="crud-table-wrap">
        <table class="crud-table">
            <thead>
                <!-- Fila de títulos + ordenación -->
                <tr>
                    <th class="crud-th crud-th--actions" colspan="2">Acciones</th>
                    <?php foreach ($manyToMany as $mtm): ?>
                        <th class="crud-th"><?= esc($mtm['label']) ?></th>
                    <?php endforeach; ?>
                    <?php foreach ($visibleColumns as $col): ?>
                        <?php
                            $name   = $col['column_name'];
                            $newDir = ($orderBy === $name && $orderDir === 'ASC') ? 'DESC' : 'ASC';
                        ?>
                        <th class="crud-th">
                            <a href="<?= crudUrl(['orderBy' => $name, 'orderDir' => $newDir, 'page' => 1]) ?>"
                            class="crud-sort-link">
                                <?= esc($name) ?>
                                <?= sortIcon($name, $orderBy, $orderDir) ?>
                            </a>
                        </th>
                    <?php endforeach ?>
                </tr>
                <!-- Fila de filtros -->
                <tr class="crud-filter-row">
                    <th colspan="<?= 2 + count($manyToMany) ?>"></th>
                    <?php foreach ($visibleColumns as $col): ?>
                        <?php $name = $col['column_name']; ?>
                        <th class="crud-th--filter">
                            <form method="get" action="">
                                <?php foreach ($_GET as $k => $v): ?>
                                    <?php if ($k !== $name && $k !== 'page'): ?>
                                        <input type="hidden" name="<?= esc($k) ?>" value="<?= esc($v) ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <input type="hidden" name="page" value="1">
                                <input type="text"
                                    name="<?= esc($name) ?>"
                                    value="<?= esc($filters[$name] ?? '') ?>"
                                    class="crud-filter-input"
                                    placeholder="Filtrar..."
                                    title="Filtrar por <?= esc($name) ?>">
                            </form>
                        </th>
                    <?php endforeach ?>
                </tr>
            </thead>

            <?php if (empty($rows)): ?>
                <tbody>
                    <tr>
                        <td colspan="<?= count($visibleColumns) + count($manyToMany) + 2 ?>" class="crud-empty">
                            No hay registros que mostrar.
                        </td>
                    </tr>
                </tbody>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tbody>
                        <tr class="crud-row">
                            <!-- Editar -->
                            <td class="crud-td--action">
                                <button class="btn-icon btn-icon--edit"
                                        title="Editar"
                                        @click='openEdit(<?= json_encode($row) ?>)'>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                            </td>
                            <!-- Borrar -->
                            <td class="crud-td--action">
                                <button class="btn-icon btn-icon--delete"
                                        title="Eliminar"
                                        @click="openDeleteModal(<?= $row[$primaryKey] ?>)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </td>
                            <!-- Botones MTM -->
                            <?php foreach ($manyToMany as $mtm): ?>
                                <td class="crud-td--action">
                                    <button class="btn-icon"
                                            title="<?= esc($mtm['label']) ?>"
                                            @click="toggleMtm(<?= $row[$primaryKey] ?>, '<?= esc($mtm['pivotTable']) ?>')">
                                        <?= esc($mtm['label']) ?>
                                    </button>
                                </td>
                            <?php endforeach; ?>
                            <!-- Datos -->
                            <?php foreach ($visibleColumns as $col): ?>
                                <?php
                                    $name    = $col['column_name'];
                                    $display = isset($foreignKeys[$name])
                                        ? ($row['__fk_' . strtolower($name)] ?? $row[$name])
                                        : $row[$name];
                                ?>
                                <td class="crud-td"><?= esc($display ?? '') ?></td>
                            <?php endforeach ?>
                        </tr>

                        <!-- Filas acordeón MTM -->
                        <?php foreach ($manyToMany as $mtm): ?>
                            <template x-if="openMtmId === <?= $row[$primaryKey] ?> && openMtmPivot === '<?= esc($mtm['pivotTable']) ?>'">
                                <tr class="crud-row--mtm">
                                    <td colspan="<?= count($visibleColumns) + count($manyToMany) + 2 ?>">
                                        <div class="crud-mtm-panel">
                                            <strong><?= esc($mtm['label']) ?></strong>

                                            <table class="crud-table mt-2 mb-3">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th style="width:60px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="item in mtmAssigned" :key="item.pivot_id">
                                                        <tr>
                                                            <td x-text="item[mtmDisplay]"></td>
                                                            <td>
                                                                <button type="button"
                                                                        class="btn-icon btn-icon--delete"
                                                                        @click.prevent="mtmRemove(item.pivot_id)">
                                                                    &times;
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template x-if="mtmAssigned.length === 0">
                                                        <tr>
                                                            <td colspan="2" class="crud-empty">Sin asignaciones</td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>

                                            <div class="crud-mtm-add">
                                                <select x-model="mtmSelected" class="crud-filter-input">
                                                    <option value="">— Seleccionar —</option>
                                                    <template x-for="item in mtmAvailable" :key="item[mtmRemoteKey]">
                                                        <option :value="item[mtmRemoteKey]"
                                                                x-text="item[mtmDisplay]"></option>
                                                    </template>
                                                </select>
                                                <button type="button"
                                                        class="btn-icon btn-icon--edit"
                                                        @click.prevent="mtmAdd()">
                                                    + Añadir
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        <?php endforeach; ?>
                    </tbody>
                <?php endforeach ?>
            <?php endif ?>

        </table>
    </div>

    <!-- ── Paginación ───────────────────────────────────────── -->
    <?php if ($pages > 1): ?>
    <div class="crud-pagination">
        <?php if ($page > 1): ?>
            <a href="<?= crudUrl(['page' => $page - 1]) ?>" class="crud-page-btn">← Anterior</a>
        <?php endif ?>

        <?php
            $start = max(1, $page - 2);
            $end   = min($pages, $page + 2);
        ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="<?= crudUrl(['page' => $i]) ?>"
               class="crud-page-btn <?= $i === $page ? 'crud-page-btn--active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor ?>

        <?php if ($page < $pages): ?>
            <a href="<?= crudUrl(['page' => $page + 1]) ?>" class="crud-page-btn">Siguiente →</a>
        <?php endif ?>
    </div>
    <?php endif ?>

    <!-- ── Modal inserción / edición ───────────────────────── -->
    <?php include APPPATH . 'Views/crud/_modal.php' ?>
    <!-- ── Modal de Confirmación de Borrado ───────────────────────── -->
<!-- ── Modal de Confirmación de Borrado ───────────────────────── -->
<div class="modal"
     x-show="showDeleteModal"
     x-transition.opacity.duration.200ms
     style="display: none;">

    <!-- Overlay oscuro (clic fuera para cerrar) -->
    <div class="modal-overlay"
         @click="closeDeleteModal()">
    </div>

    <div class="modal-box"
         x-show="showDeleteModal">
        <div class="modal-header">
            <h2 class="modal-title">
                Confirmar eliminación
            </h2>

            <button type="button"
                    class="modal-close"
                    @click="closeDeleteModal()">
                &times;
            </button>
        </div>

        <div class="modal-body">
            <p class="modal-text">
                ¿Seguro que quieres eliminar este registro?
                <br>
                <small>Esta acción no se puede deshacer.</small>
            </p>
        </div>

        <div class="modal-footer">
            <button type="button"
                    class="btn btn-secondary"
                    @click="closeDeleteModal()">
                Cancelar
            </button>

            <button type="button"
                    class="btn btn-danger"
                    @click="confirmDelete()">
                Sí, eliminar
            </button>
        </div>
    </div>
</div>

</div>

<!-- ── CSS inline de la tabla ───────────────────────────────── -->
<style>
.crud-wrapper { font-size: .9rem; }

.crud-toolbar
{
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.crud-toolbar__actions { display: flex; align-items: center; gap: .75rem; }
.crud-label { display: flex; align-items: center; gap: .4rem; font-size: .85rem; color: var(--color-muted, #666); }
.crud-select { padding: .25rem .4rem; border: 1px solid var(--color-border, #ccc); border-radius: 4px; }

.crud-table-wrap { overflow-x: auto; }

.crud-table
{
    width: 100%;
    border-collapse: collapse;
    font-size: .875rem;
}
.crud-table thead { background: var(--color-surface, #f9f9f9); }
.crud-th
{
    padding: .5rem .75rem;
    text-align: left;
    border-bottom: 2px solid var(--color-border, #ddd);
    white-space: nowrap;
}
.crud-th--actions { width: 60px; }
.crud-th--filter { padding: .25rem .5rem; }

.crud-sort-link { color: inherit; text-decoration: none; display: flex; align-items: center; gap: .3rem; }
.crud-sort-link:hover { color: var(--color-primary, #01696f); }
.sort-icon { opacity: .4; font-size: .8rem; }
.sort-icon.active { opacity: 1; color: var(--color-primary, #01696f); }

.crud-filter-input
{
    width: 100%;
    padding: .25rem .4rem;
    border: 1px solid var(--color-border, #ddd);
    border-radius: 4px;
    font-size: .8rem;
}
.crud-filter-input:focus { outline: 2px solid var(--color-primary, #01696f); }

.crud-row:hover { background: var(--color-active-bg, #f0f0f0); }
.crud-row--mtm td
{
    transition: opacity 0.2s ease;
}
.crud-td { padding: .45rem .75rem; border-bottom: 1px solid var(--color-border, #eee); }
.crud-td--action { padding: .3rem .4rem; border-bottom: 1px solid var(--color-border, #eee); text-align: center; }

.crud-empty { text-align: center; padding: 2rem; color: var(--color-muted, #999); }

.btn-icon
{
    background: none;
    border: none;
    cursor: pointer;
    padding: .25rem;
    border-radius: 4px;
    color: var(--color-muted, #666);
    transition: color .15s, background .15s;
}
.btn-icon--edit:hover  { color: var(--color-primary, #01696f); background: var(--color-active-bg, #e6f0ef); }
.btn-icon--delete:hover{ color: var(--color-error, #a12c2c);   background: #fde8e8; }

/* Paginación */
.crud-pagination { display: flex; gap: .4rem; margin-top: 1rem; flex-wrap: wrap; }
.crud-page-btn
{
    padding: .3rem .65rem;
    border: 1px solid var(--color-border, #ddd);
    border-radius: 4px;
    text-decoration: none;
    color: var(--color-text, #333);
    font-size: .85rem;
}
.crud-page-btn:hover { background: var(--color-active-bg, #f0f0f0); }
.crud-page-btn--active
{
    background: var(--color-primary, #01696f);
    color: #fff;
    border-color: var(--color-primary, #01696f);
}

/* Botón nuevo */
.btn { display: inline-flex; align-items: center; gap: .4rem; padding: .4rem .9rem;
       border: none; border-radius: 4px; cursor: pointer; font-size: .875rem; font-weight: 500; }
.btn-primary { background: var(--color-primary, #01696f); color: #fff; }
.btn-primary:hover { background: var(--color-primary-dark, #0c4e54); }
.modal-box
{
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 12px 40px rgba(0,0,0,.2);
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    z-index: 1001;
}
</style>
<!-- ── Alpine.js component ──────────────────────────────────── -->
<script>
function crudTable()
{
    return {
        isSaving:        false,
        showModal:       false,
        isEdit:          false,
        formData:        {},
        formErrors:      {},
        primaryKey:      <?= json_encode($primaryKey) ?>,
        saveUrl:         <?= json_encode(site_url(uri_string() . '/save')) ?>,
        deleteUrl:       <?= json_encode(site_url(uri_string() . '/delete')) ?>,
        editColumns:     <?= json_encode($editColumns) ?>,
        insertColumns:   <?= json_encode($insertColumns) ?>,
        fkOptions:       <?= json_encode($fkOptions) ?>,
        showDeleteModal: false,
        deleteId:        null,
        passwordFields:  <?= json_encode($passwordFields ?? []) ?>,

        // ── MTM ──────────────────────────────────────────
        openMtmId:    null,
        openMtmPivot: null,
        mtmLocalId:   null,
        mtmAssigned:  [],
        mtmAvailable: [],
        mtmDisplay:   'id',
        mtmRemoteKey: 'id',
        mtmSelected:  '',
        mtmBaseUrl:   '',
        mtmAdding:    false,

        openDeleteModal(id)
        {
            this.deleteId        = id;
            this.showDeleteModal = true;
        },

        closeDeleteModal()
        {
            this.showDeleteModal = false;
            this.deleteId        = null;
        },

        init()
        {
            document.querySelectorAll('.crud-filter-input').forEach(input =>
            {
                let t;
                input.addEventListener('input', () =>
                {
                    clearTimeout(t);
                    t = setTimeout(() => input.closest('form').submit(), 400);
                });
            });
        },

        openInsert()
        {
            this.isEdit     = false;
            this.formData   = {};
            this.formErrors = {};

            this.insertColumns.forEach(col =>
            {
                if ((col.data_type ?? '').includes('bool'))
                {
                    this.formData[col.column_name] = false;
                }
            });

            this.showModal = true;
        },

        openEdit(row)
        {
            this.isEdit     = true;
            this.formData   = { ...row };
            this.formErrors = {};
            this.showModal  = true;
        },

        closeModal()
        {
            this.showModal = false;
        },

        async saveRow()
        {
            // EL CANDADO: Evita que se guarde dos veces
            if (this.isSaving) return; 
            this.isSaving = true;
              console.trace('🔍 ¿QUIÉN ESTÁ LLAMANDO A SAVE_ROW?'); // <--- AÑADE ESTA LÍNEA
            this.formErrors = {};
            const cols = this.isEdit ? this.editColumns : this.insertColumns;
            const fd   = new FormData();

            if (this.isEdit && this.formData[this.primaryKey])
            {
                fd.append(this.primaryKey, this.formData[this.primaryKey]);
            }

            cols.forEach(col =>
            {
                const val = this.formData[col.column_name] ?? '';
                fd.append(col.column_name, val);
            });

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) fd.append(csrfMeta.getAttribute('name'), csrfMeta.getAttribute('content'));

            try
            {
                const res  = await fetch(this.saveUrl, { method: 'POST', body: fd });
                const json = await res.json();

                if (json.ok)
                {
                    this.closeModal();
                    // Pequeño retardo para que el modal se cierre visualmente antes de recargar
                    setTimeout(() => location.reload(), 100);
                }
                else
                {
                    this.formErrors = json.errors ?? {};
                    this.isSaving = false; 
                }
            }
            catch (e)
            {
                console.error('saveRow error:', e);
                this.formErrors = { _global: 'Error de red. Inténtalo de nuevo.' };
                this.isSaving = false;
            }
        },

        async confirmDelete()
        {
            if (! this.deleteId) return;

            const fd = new FormData();
            fd.append(this.primaryKey, this.deleteId);

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta)
            {
                fd.append(csrfMeta.getAttribute('name'), csrfMeta.getAttribute('content'));
            }

            try
            {
                const res  = await fetch(this.deleteUrl, { method: 'POST', body: fd });
                const json = await res.json();

                if (json.ok)
                {
                    location.reload();
                }
                else
                {
                    alert('No se pudo eliminar: ' + JSON.stringify(json.errors));
                }
            }
            catch (e)
            {
                alert('Error de red.');
            }
            finally
            {
                this.closeDeleteModal();
            }
        },

        isFk(colName)
        {
            return this.fkOptions[colName] !== undefined;
        },

        getInputType(col)
        {
            const t = col.data_type ?? '';
            if (t.includes('int') || t.includes('numeric') || t.includes('double')) return 'number';
            if (t.includes('date')) return 'date';
            if (t.includes('bool')) return 'checkbox';
            return 'text';
        },

        getMaxLength(col)
        {
            return col.character_maximum_length ?? null;
        },

        isRequired(col)
        {
            return col.is_nullable === 'NO';
        },

        // ── Métodos MTM ───────────────────────────────────
        toggleMtm(localId, pivotTable)
        {
            if (this.openMtmId === localId && this.openMtmPivot === pivotTable)
            {
                this.openMtmId    = null;
                this.openMtmPivot = null;
                this.mtmAssigned  = [];
                this.mtmAvailable = [];
                this.mtmSelected  = '';
                return;
            }

            this.mtmAssigned  = [];
            this.mtmAvailable = [];
            this.mtmSelected  = '';

            this.openMtmId    = localId;
            this.openMtmPivot = pivotTable;
            this.mtmLocalId   = localId;
            this.mtmBaseUrl   = window.location.pathname.replace(/\/$/, '');

            this.mtmLoad(pivotTable, localId);
        },

        async mtmLoad(pivotTable, localId)
        {
            try
            {
                const res  = await fetch(this.mtmBaseUrl + '/mtm/get', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    new URLSearchParams({ pivotTable, localId }),
                });

                if (! res.ok)
                {
                    console.error('mtmLoad HTTP error:', res.status);
                    return;
                }

                const data = await res.json();

                this.mtmAssigned  = Array.isArray(data.assigned)  ? data.assigned  : [];
                this.mtmAvailable = Array.isArray(data.available) ? data.available : [];
                this.mtmDisplay   = data.display ?? 'id';

                const sample      = (this.mtmAvailable[0] ?? this.mtmAssigned[0]) ?? null;
                this.mtmRemoteKey = sample
                    ? Object.keys(sample).find(k => k !== 'pivot_id' && k !== this.mtmDisplay) ?? 'id'
                    : 'id';
            }
            catch (e)
            {
                console.error('mtmLoad error:', e);
                this.mtmAssigned  = [];
                this.mtmAvailable = [];
            }
        },

        async mtmAdd()
        {
            if (! this.mtmSelected || this.mtmAdding) return;
            this.mtmAdding = true;

            try
            {
                const res  = await fetch(this.mtmBaseUrl + '/mtm/add', {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body:    new URLSearchParams({
                        pivotTable: this.openMtmPivot,
                        localId:    this.mtmLocalId,
                        remoteId:   this.mtmSelected,
                    }),
                });
                const data = await res.json();
            }
            finally
            {
                this.mtmAdding   = false;
                this.mtmSelected = '';
                this.mtmLoad(this.openMtmPivot, this.mtmLocalId);
            }
        },

        async mtmRemove(pivotId)
        {
            await fetch(this.mtmBaseUrl + '/mtm/remove', {
                method:  'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body:    new URLSearchParams({
                    pivotTable: this.openMtmPivot,
                    pivotId,
                }),
            });
            this.mtmLoad(this.openMtmPivot, this.mtmLocalId);
        },
    }
}
</script>

