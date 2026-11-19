<?php
/**
 * Modal genérico de inserción / edición.
 * Incluido desde crud/index.php — tiene acceso a todas sus variables.
 */
?>

<!-- Overlay del modal -->
<div x-show="showModal"
     x-transition:enter="modal-overlay-enter"
     x-transition:enter-start="modal-overlay-start"
     x-transition:enter-end="modal-overlay-end"
     x-transition:leave="modal-overlay-enter"
     x-transition:leave-start="modal-overlay-end"
     x-transition:leave-end="modal-overlay-start"
     class="modal-overlay"
     @click.self="closeModal()"
     style="display:none">

    <div class="modal-box"
         role="dialog"
         aria-modal="true"
         aria-labelledby="crud-modal-title"
         x-transition:enter="modal-box-enter"
         x-transition:enter-start="modal-box-start"
         x-transition:enter-end="modal-box-end"
         @click.stop>

        <!-- Cabecera -->
        <div class="modal-header">
            <h3 id="crud-modal-title"
                class="modal-title"
                x-text="isEdit ? 'Editar registro' : 'Nuevo registro'"></h3>

            <button type="button"
                    class="modal-close"
                    @click="closeModal()"
                    title="Cerrar"
                    aria-label="Cerrar">
                <svg width="18"
                     height="18"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2.5"
                     aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Contenedor flexible: cuerpo desplazable y pie visible -->
        <div class="modal-content">

            <div class="modal-body">

                <!-- Error global -->
                <div x-show="formErrors._global"
                     class="modal-error-global"
                     x-text="formErrors._global"
                     role="alert"
                     style="display:none"></div>

                <!-- Edición -->
                <template x-if="isEdit">
                    <div class="modal-fields">
                        <?php foreach ($editColumns as $col): ?>
                            <?php
                                $name     = $col['column_name'];
                                $type     = $col['data_type'] ?? '';
                                $maxlen   = $col['character_maximum_length'] ?? null;
                                $nullable = $col['is_nullable'] === 'YES';
                                $isFk     = isset($foreignKeys[$name]);
                            ?>

                            <div class="modal-field">
                                <label class="modal-label"
                                       for="edit_<?= esc($name) ?>">
                                    <?= esc($name) ?>
                                    <?php if (! $nullable): ?>
                                        <span class="modal-required">*</span>
                                    <?php endif ?>
                                </label>

                                <?php if ($isFk): ?>

                                    <!-- SELECT para FK -->
                                    <select id="edit_<?= esc($name) ?>"
                                            x-model="formData.<?= $name ?>"
                                            class="modal-input"
                                            <?= ! $nullable ? 'required' : '' ?>>
                                        <option value="">— Seleccionar —</option>

                                        <?php foreach ($fkOptions[$name] as $opt): ?>
                                            <option value="<?= esc($opt['id']) ?>">
                                                <?= esc($opt['label']) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>

                                <?php elseif (str_contains($type, 'bool')): ?>

                                    <!-- CHECKBOX para boolean -->
                                    <input type="checkbox"
                                           id="edit_<?= esc($name) ?>"
                                           x-model="formData.<?= $name ?>"
                                           class="modal-checkbox">

                                <?php else: ?>

                                    <!-- INPUT genérico -->
                                    <input id="edit_<?= esc($name) ?>"
                                           x-model="formData.<?= $name ?>"
                                           class="modal-input"
                                           :class="
                                           {
                                               'modal-input--error': formErrors.<?= $name ?>
                                           }"
                                           type="<?php
                                               if (in_array($name, $passwordFields ?? []))
                                               {
                                                   echo 'password';
                                               }
                                               elseif (preg_match('/int|numeric|double/', $type))
                                               {
                                                   echo 'number';
                                               }
                                               elseif (str_contains($type, 'date'))
                                               {
                                                   echo 'date';
                                               }
                                               else
                                               {
                                                   echo 'text';
                                               }
                                           ?>"
                                           <?= $maxlen ? 'maxlength="' . esc($maxlen) . '"' : '' ?>
                                           <?= ! $nullable ? 'required' : '' ?>>

                                <?php endif ?>

                                <!-- Mensaje de error del campo -->
                                <span class="modal-field-error"
                                      x-show="formErrors.<?= $name ?>"
                                      x-text="formErrors.<?= $name ?>"
                                      style="display:none"></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </template>

                <!-- Inserción -->
                <template x-if="!isEdit">
                    <div class="modal-fields">
                        <?php foreach ($insertColumns as $col): ?>
                            <?php
                                $name     = $col['column_name'];
                                $type     = $col['data_type'] ?? '';
                                $maxlen   = $col['character_maximum_length'] ?? null;
                                $nullable = $col['is_nullable'] === 'YES';
                                $isFk     = isset($foreignKeys[$name]);
                            ?>

                            <div class="modal-field">
                                <label class="modal-label"
                                       for="ins_<?= esc($name) ?>">
                                    <?= esc($name) ?>
                                    <?php if (! $nullable): ?>
                                        <span class="modal-required">*</span>
                                    <?php endif ?>
                                </label>

                                <?php if ($isFk): ?>

                                    <!-- SELECT para FK -->
                                    <select id="ins_<?= esc($name) ?>"
                                            x-model="formData.<?= $name ?>"
                                            class="modal-input"
                                            <?= ! $nullable ? 'required' : '' ?>>
                                        <option value="">— Seleccionar —</option>

                                        <?php foreach ($fkOptions[$name] as $opt): ?>
                                            <option value="<?= esc($opt['id']) ?>">
                                                <?= esc($opt['label']) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>

                                <?php elseif (str_contains($type, 'bool')): ?>

                                    <!-- CHECKBOX para boolean -->
                                    <input type="checkbox"
                                           id="ins_<?= esc($name) ?>"
                                           x-model="formData.<?= $name ?>"
                                           class="modal-checkbox">

                                <?php else: ?>

                                    <!-- INPUT genérico -->
                                    <input id="ins_<?= esc($name) ?>"
                                           x-model="formData.<?= $name ?>"
                                           class="modal-input"
                                           :class="
                                           {
                                               'modal-input--error': formErrors.<?= $name ?>
                                           }"
                                           type="<?php
                                               if (in_array($name, $passwordFields ?? []))
                                               {
                                                   echo 'password';
                                               }
                                               elseif (preg_match('/int|numeric|double/', $type))
                                               {
                                                   echo 'number';
                                               }
                                               elseif (str_contains($type, 'date'))
                                               {
                                                   echo 'date';
                                               }
                                               else
                                               {
                                                   echo 'text';
                                               }
                                           ?>"
                                           <?= $maxlen ? 'maxlength="' . esc($maxlen) . '"' : '' ?>
                                           <?= ! $nullable ? 'required' : '' ?>>

                                <?php endif ?>

                                <!-- Mensaje de error del campo -->
                                <span class="modal-field-error"
                                      x-show="formErrors.<?= $name ?>"
                                      x-text="formErrors.<?= $name ?>"
                                      style="display:none"></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </template>

            </div>

            <!-- Pie: fuera de la zona desplazable -->
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-ghost"
                        @click="closeModal()">
                    Cancelar
                </button>

                <button type="button"
                        class="btn btn-primary"
                        @click="saveRow()"
                        :disabled="isSaving"
                        x-text="isSaving ? 'Guardando…' : 'Guardar'">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- CSS del modal -->
<style>
.modal-overlay
{
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .45);
    z-index: 1000;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 1rem;
    box-sizing: border-box;
}

.modal-box
{
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 12px 40px rgba(0, 0, 0, .2);

    width: 100%;
    max-width: 520px;
    max-height: calc(100vh - 2rem);
    max-height: calc(100dvh - 2rem);
    min-height: 0;

    display: flex;
    flex-direction: column;

    overflow: hidden;
    box-sizing: border-box;
}

.modal-header
{
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 0 0 auto;
    gap: 1rem;

    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--color-border, #ddd);
}

.modal-title
{
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.modal-close
{
    flex: 0 0 auto;
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    padding: .25rem;
    border-radius: 4px;
}

.modal-close:hover
{
    background: #f0f0f0;
}

/* Este contenedor también debe poder reducir su altura. */
.modal-content
{
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;

    min-height: 0;
    overflow: hidden;
}

/* Única zona del modal con desplazamiento vertical. */
.modal-body
{
    flex: 1 1 auto;
    min-height: 0;

    padding: 1.25rem;
    overflow-y: auto;
    overscroll-behavior-y: contain;
    box-sizing: border-box;
}

.modal-fields
{
    display: flex;
    flex-direction: column;
    gap: .9rem;
}

.modal-field
{
    display: flex;
    flex-direction: column;
    gap: .3rem;
}

.modal-label
{
    font-size: .85rem;
    font-weight: 500;
    color: #444;
}

.modal-required
{
    color: var(--color-error, #a12c2c);
    margin-left: .2rem;
}

.modal-input
{
    padding: .45rem .65rem;
    border: 1px solid var(--color-border, #ccc);
    border-radius: 4px;
    font-size: .9rem;
    width: 100%;
    box-sizing: border-box;
    transition: border-color .15s;
}

.modal-input:focus
{
    outline: 2px solid var(--color-primary, #01696f);
    border-color: transparent;
}

.modal-input--error
{
    border-color: var(--color-error, #a12c2c);
}

.modal-checkbox
{
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.modal-field-error
{
    font-size: .8rem;
    color: var(--color-error, #a12c2c);
}

.modal-error-global
{
    margin: 0 0 1rem;
    padding: .5rem .75rem;
    background: #fde8e8;
    color: var(--color-error, #a12c2c);
    border-radius: 4px;
    font-size: .85rem;
}

.modal-footer
{
    display: flex;
    justify-content: flex-end;
    flex: 0 0 auto;
    gap: .75rem;

    padding: .9rem 1.25rem;
    border-top: 1px solid var(--color-border, #ddd);
    background: #fff;
}

.modal-footer .btn:disabled
{
    opacity: .65;
    cursor: not-allowed;
}

.btn-ghost
{
    background: none;
    border: 1px solid var(--color-border, #ccc);
    color: #444;
}

.btn-ghost:hover
{
    background: #f5f5f5;
}

/* Transiciones Alpine */
.modal-overlay-enter
{
    transition: opacity .2s ease;
}

.modal-overlay-start
{
    opacity: 0;
}

.modal-overlay-end
{
    opacity: 1;
}

.modal-box-enter
{
    transition: opacity .2s ease, transform .2s ease;
}

.modal-box-start
{
    opacity: 0;
    transform: translateY(-12px);
}

.modal-box-end
{
    opacity: 1;
    transform: translateY(0);
}
</style>