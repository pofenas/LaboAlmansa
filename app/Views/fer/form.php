<?php
/**
 * Formulario independiente de reporte rápido de incidencias.
 *
 * No utiliza el layout ni el menú del mantenimiento.
 */

$sinTipos = empty($tipos);

$bloqueado = (string) ($datos['bloquea_equipo'] ?? '0') === '1';

$exito = session()->getFlashdata('fer_exito');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1, viewport-fit=cover">

    <meta name="theme-color" content="#01696f">
    <meta name="robots" content="noindex, nofollow">

    <title>Reportar incidencia · <?= esc($equipo['nombre']) ?></title>


</head>

<body>

    <header class="fer-topbar">
        <div class="fer-topbar-inner">
            <p class="fer-brand">Laboratorio</p>
            <p class="fer-brand-subtitle">
                Reporte rápido de incidencias
            </p>
        </div>
    </header>

    <main class="fer-main">

        <?php if ($exito): ?>
            <div class="fer-message fer-message--success"
                 role="status">
                <?= esc($exito) ?>
            </div>
        <?php endif ?>

        <?php if (! empty($errores)): ?>
            <div class="fer-message fer-message--error"
                 role="alert">
                <p>No se ha guardado la incidencia. Revisa los datos:</p>

                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <section class="fer-card"
                 aria-labelledby="fer-title">

            <div class="fer-heading">
                <h1 id="fer-title">Reportar incidencia</h1>
                <p>
                    Selecciona el tipo de incidencia y añade
                    los detalles que necesites.
                </p>
            </div>

            <?php if ($sinTipos): ?>
                <div class="fer-message fer-message--warning"
                     role="alert">
                    Este equipo no tiene tipos de incidencia asignados.
                    Solicita su configuración al responsable.
                </div>
            <?php endif ?>

            <form method="post"
                  action="<?= site_url('fer/' . (int) $equipo['id_equipo']) ?>"
                  class="fer-form">

                <?= csrf_field() ?>

                <div class="fer-field">
                    <label for="fer-equipo"
                           class="fer-label">
                        Equipo
                    </label>

                    <input type="text"
                           id="fer-equipo"
                           class="fer-control"
                           value="<?= esc($equipo['nombre'], 'attr') ?>"
                           readonly
                           aria-describedby="fer-equipo-hint">

                    <p id="fer-equipo-hint"
                       class="fer-hint">
                        Equipo identificado mediante el enlace del QR.
                    </p>
                </div>

                <div class="fer-field">
                    <label for="fer-fecha"
                           class="fer-label">
                        Fecha de incidencia
                    </label>

                    <input type="date"
                           id="fer-fecha"
                           name="fecha_incidencia"
                           class="fer-control"
                           value="<?= esc($datos['fecha_incidencia'] ?? '', 'attr') ?>"
                           aria-invalid="<?= isset($errores['fecha_incidencia']) ? 'true' : 'false' ?>"
                           <?= isset($errores['fecha_incidencia']) ? 'aria-describedby="fer-fecha-error"' : '' ?>
                           required>

                    <?php if (isset($errores['fecha_incidencia'])): ?>
                        <p id="fer-fecha-error"
                           class="fer-error">
                            <?= esc($errores['fecha_incidencia']) ?>
                        </p>
                    <?php endif ?>
                </div>

                <div class="fer-field">
                    <label for="fer-tipo"
                           class="fer-label">
                        Tipo de incidencia
                    </label>

                    <select id="fer-tipo"
                            name="id_tie"
                            class="fer-control"
                            aria-invalid="<?= isset($errores['id_tie']) ? 'true' : 'false' ?>"
                            <?= isset($errores['id_tie']) ? 'aria-describedby="fer-tipo-error"' : '' ?>
                            <?= $sinTipos ? 'disabled' : '' ?>
                            required>

                        <option value="">— Seleccionar —</option>

                        <?php foreach ($tipos as $tipo): ?>
                            <option
                                value="<?= (int) $tipo['id_tie'] ?>"
                                <?= (string) ($datos['id_tie'] ?? '') ===
                                    (string) $tipo['id_tie'] ? 'selected' : '' ?>>
                                <?= esc($tipo['tipo_incidencia']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <?php if (isset($errores['id_tie'])): ?>
                        <p id="fer-tipo-error"
                           class="fer-error">
                            <?= esc($errores['id_tie']) ?>
                        </p>
                    <?php endif ?>
                </div>

                <div class="fer-blocking">

                    <label class="fer-switch-label"
                           for="fer-bloquea">

                        <span class="fer-blocking-text">
                            <span class="fer-blocking-title">
                                Bloquea el equipo
                            </span>

                            <span id="fer-bloquea-hint"
                                  class="fer-blocking-hint">
                                Actívalo si el equipo no puede utilizarse.
                            </span>
                        </span>

                        <span class="fer-switch">
                            <input type="checkbox"
                                   id="fer-bloquea"
                                   name="bloquea_equipo"
                                   value="1"
                                   role="switch"
                                   aria-describedby="fer-bloquea-hint"
                                   <?= $bloqueado ? 'checked' : '' ?>>

                            <span class="fer-switch-track"
                                  aria-hidden="true"></span>
                        </span>
                    </label>

                    <div class="fer-blocking-state"
                         aria-hidden="true">
                        <span class="fer-state-off">
                            No bloquea
                        </span>

                        <span class="fer-state-on">
                            Equipo bloqueado
                        </span>
                    </div>
                </div>

                <div class="fer-field">
                    <label for="fer-incidencia"
                           class="fer-label">
                        Explicación adicional
                        <span class="fer-optional">(opcional)</span>
                    </label>

                    <textarea id="fer-incidencia"
                              name="incidencia"
                              class="fer-control"
                              rows="5"
                              maxlength="10000"
                              aria-invalid="<?= isset($errores['incidencia']) ? 'true' : 'false' ?>"
                              <?= isset($errores['incidencia']) ? 'aria-describedby="fer-incidencia-error"' : '' ?>
                              placeholder="¿Qué ha ocurrido? Añade cualquier detalle que pueda ayudar."
                    ><?= esc($datos['incidencia'] ?? '') ?></textarea>

                    <?php if (isset($errores['incidencia'])): ?>
                        <p id="fer-incidencia-error"
                           class="fer-error">
                            <?= esc($errores['incidencia']) ?>
                        </p>
                    <?php endif ?>
                </div>

                <button type="submit"
                        class="fer-submit"
                        <?= $sinTipos ? 'disabled' : '' ?>>
                    Guardar incidencia
                </button>

            </form>
        </section>

        <footer class="fer-footer">
            FER · Registro rápido de incidencias de equipos
        </footer>

    </main>
</body>
</html>
    <style>
        :root
        {
            color-scheme: light;

            --fer-primary: #01696f;
            --fer-primary-dark: #00545a;
            --fer-text: #213238;
            --fer-muted: #596b71;
            --fer-border: #bdcbd0;
            --fer-background: #f0f5f5;
            --fer-danger: #a12c2c;
        }

        *
        {
            box-sizing: border-box;
        }

        body
        {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;

            background: var(--fer-background);
            color: var(--fer-text);

            font-family: system-ui, -apple-system, BlinkMacSystemFont,
                        "Segoe UI", sans-serif;
            font-size: 16px;
            line-height: 1.5;

            overflow-wrap: anywhere;
        }

        button,
        input,
        select,
        textarea
        {
            font: inherit;
        }

        .fer-topbar
        {
            padding: 1rem;
            padding-top: max(1rem, env(safe-area-inset-top));

            background: var(--fer-primary);
            color: #fff;
        }

        .fer-topbar-inner
        {
            max-width: 560px;
            margin: 0 auto;
        }

        .fer-brand
        {
            margin: 0;
            font-size: .95rem;
            font-weight: 600;
        }

        .fer-brand-subtitle
        {
            margin: .15rem 0 0;
            font-size: .85rem;
            opacity: .9;
        }

        .fer-main
        {
            width: 100%;
            max-width: 592px;
            margin: 0 auto;

            padding: 1rem;
            padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
        }

        .fer-card
        {
            padding: 1.25rem;
            border: 1px solid #dae4e6;
            border-radius: 14px;

            background: #fff;
            box-shadow: 0 4px 18px rgba(20, 50, 55, .05);
        }

        .fer-heading
        {
            margin-bottom: 1.5rem;
        }

        .fer-heading h1
        {
            margin: 0 0 .4rem;
            font-size: 1.45rem;
            line-height: 1.25;
        }

        .fer-heading p
        {
            margin: 0;
            color: var(--fer-muted);
            font-size: .95rem;
        }

        .fer-form
        {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .fer-field
        {
            display: flex;
            flex-direction: column;
            gap: .45rem;
            min-width: 0;
        }

        .fer-label
        {
            font-weight: 650;
        }

        .fer-optional
        {
            color: var(--fer-muted);
            font-size: .875rem;
            font-weight: 400;
        }

        .fer-control
        {
            display: block;
            width: 100%;
            min-width: 0;
            min-height: 48px;

            padding: .75rem;
            border: 1px solid var(--fer-border);
            border-radius: 8px;

            background: #fff;
            color: var(--fer-text);

            font-size: 16px;
        }

        .fer-control:focus
        {
            outline: 3px solid rgba(1, 105, 111, .25);
            outline-offset: 2px;
            border-color: var(--fer-primary);
        }

        .fer-control[readonly]
        {
            background: #edf4f4;
            border-color: #cedede;
            font-weight: 600;
        }

        .fer-control:disabled
        {
            background: #f1f1f1;
            color: #666;
        }

        textarea.fer-control
        {
            min-height: 140px;
            resize: vertical;
        }

        .fer-hint
        {
            margin: 0;
            color: var(--fer-muted);
            font-size: .85rem;
        }

        .fer-error
        {
            margin: 0;
            color: var(--fer-danger);
            font-size: .9rem;
        }

        .fer-control[aria-invalid="true"]
        {
            border-color: var(--fer-danger);
        }

        .fer-blocking
        {
            padding: 1rem;
            border: 1px solid #d7e1e3;
            border-radius: 10px;
            background: #f7fafa;
        }

        .fer-switch-label
        {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;

            min-height: 48px;
            cursor: pointer;
        }

        .fer-blocking-text
        {
            display: flex;
            flex-direction: column;
            gap: .3rem;
        }

        .fer-blocking-title
        {
            font-weight: 650;
        }

        .fer-blocking-hint
        {
            color: var(--fer-muted);
            font-size: .875rem;
        }

        .fer-switch
        {
            position: relative;
            display: inline-block;

            flex: 0 0 60px;
            width: 60px;
            height: 34px;
        }

        .fer-switch input
        {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            opacity: 0;
            z-index: 1;
            cursor: pointer;
        }

        .fer-switch-track
        {
            position: absolute;
            inset: 0;
            border-radius: 34px;

            background: #67777d;
            transition: background .15s ease;
            pointer-events: none;
        }

        .fer-switch-track::before
        {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;

            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #fff;

            transition: transform .15s ease;
        }

        .fer-switch input:checked + .fer-switch-track
        {
            background: var(--fer-danger);
        }

        .fer-switch input:checked + .fer-switch-track::before
        {
            transform: translateX(26px);
        }

        .fer-switch input:focus-visible + .fer-switch-track
        {
            outline: 3px solid var(--fer-primary);
            outline-offset: 3px;
        }

        .fer-blocking-state
        {
            margin-top: .5rem;
            font-size: .85rem;
            font-weight: 600;
            text-align: right;
            color: var(--fer-muted);
        }

        .fer-state-on
        {
            display: none;
            color: var(--fer-danger);
        }

        .fer-blocking:has(input:checked) .fer-state-on
        {
            display: inline;
        }

        .fer-blocking:has(input:checked) .fer-state-off
        {
            display: none;
        }

        .fer-submit
        {
            width: 100%;
            min-height: 52px;
            margin-top: .25rem;
            padding: .85rem 1rem;

            border: 0;
            border-radius: 8px;

            background: var(--fer-primary);
            color: #fff;

            font-weight: 650;
            cursor: pointer;
            touch-action: manipulation;
        }

        .fer-submit:hover
        {
            background: var(--fer-primary-dark);
        }

        .fer-submit:focus-visible
        {
            outline: 3px solid #213238;
            outline-offset: 3px;
        }

        .fer-submit:disabled
        {
            opacity: .55;
            cursor: not-allowed;
        }

        .fer-message
        {
            margin: 0 0 1.25rem;
            padding: 1rem;
            border-radius: 8px;
        }

        .fer-message p
        {
            margin: 0;
        }

        .fer-message ul
        {
            margin: .5rem 0 0;
            padding-left: 1.25rem;
        }

        .fer-message--success
        {
            background: #e7f4ec;
            color: #205c36;
            border: 1px solid #bbdcc6;
        }

        .fer-message--error
        {
            background: #fde8e8;
            color: #8a2424;
            border: 1px solid #efc3c3;
        }

        .fer-message--warning
        {
            background: #fff3cd;
            color: #705600;
            border: 1px solid #ead89d;
        }

        .fer-footer
        {
            margin-top: 1rem;
            color: var(--fer-muted);
            font-size: .8rem;
            text-align: center;
        }

        @media (max-width: 360px)
        {
            .fer-main
            {
                padding-left: .5rem;
                padding-right: .5rem;
            }

            .fer-card
            {
                padding: 1rem;
            }
        }

        @media (prefers-reduced-motion: reduce)
        {
            .fer-switch-track,
            .fer-switch-track::before
            {
                transition: none;
            }
        }
    </style>