<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Préstamos · GAI Almansa</title>
    <style>
        :root {
            --azul:        #0b5ea8;
            --azul-oscuro: #07437a;
            --azul-fila:   #e6f0fa;
            --rojo-fila:   #fbe6e6;
            --verde:       #1f9d7a;
            --fondo:       #f4f7fa;
            --texto:       #1c2b36;
            --texto-suave: #5a6b78;
            --borde:       #dbe3ea;
            --blanco:      #ffffff;
            --sombra:      0 1px 3px rgba(16,40,60,.08), 0 4px 12px rgba(16,40,60,.05);
            --radio:       12px;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0; background: var(--fondo); color: var(--texto);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            font-size: 16px; line-height: 1.45;
            -webkit-text-size-adjust: 100%; overflow-x: hidden;
        }
        .cabecera {
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
            color: var(--blanco);
            padding: 14px 18px; padding-top: max(14px, env(safe-area-inset-top));
            display: flex; align-items: center; gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            position: sticky; top: 0; z-index: 10;
        }
        .cabecera h1 { font-size: 1.05rem; font-weight: 600; margin: 0; }
        .cabecera .sub { font-size: .78rem; opacity: .85; margin-top: 2px; }
        main { max-width: 1400px; margin: 0 auto; padding: 18px 24px 40px; }
        h2.bloque {
            font-size: 1rem; margin: 24px 4px 10px;
            color: var(--texto-suave); text-transform: uppercase;
            letter-spacing: .5px;
        }
        table.prestamos {
           width: 100%; border-collapse: collapse;
           background: var(--blanco); border-radius: var(--radio);
           box-shadow: var(--sombra); overflow: hidden;
           table-layout: fixed;
        }
        table.prestamos th, table.prestamos td {
            padding: 10px 12px; text-align: left;
            font-size: .9rem; border-bottom: 1px solid var(--borde);
            word-wrap: break-word; overflow-wrap: anywhere;
        }
        table.prestamos th {
            background: #f7fafc; font-size: .78rem;
            text-transform: uppercase; letter-spacing: .5px;
            color: var(--texto-suave);
        }
        table.prestamos tr.origen-almansa { background: var(--azul-fila); }
        table.prestamos tr.destino-almansa { background: var(--rojo-fila); }
        table.prestamos tr:last-child td { border-bottom: none; }
        .acciones { white-space: nowrap; }
        .btn {
            display: inline-block; padding: 6px 10px; border-radius: 8px;
            border: 1px solid var(--borde); background: var(--blanco);
            color: var(--texto); font-size: .82rem; cursor: pointer;
            text-decoration: none; margin-right: 4px;
        }
        .btn:hover { border-color: var(--azul); color: var(--azul); }
        .btn.peligro:hover { border-color: #b3261e; color: #b3261e; }
        .btn.devolver:hover { border-color: var(--verde); color: var(--verde); }

        /* Modal */
        .modal-fondo {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.4); z-index: 100;
            align-items: center; justify-content: center; padding: 16px;
        }
        .modal-fondo.abierto { display: flex; }
        .modal {
            background: var(--blanco); border-radius: var(--radio);
            width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0,0,0,.25);
        }
        .modal h3 {
            margin: 0; padding: 16px 18px;
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
            color: var(--blanco); font-size: 1rem;
        }
        .modal .cuerpo { padding: 16px 18px; }
        .modal label {
            display: block; font-size: .82rem; color: var(--texto-suave);
            text-transform: uppercase; letter-spacing: .5px;
            margin: 12px 0 4px;
        }
        .modal input, .modal select, .modal textarea {
            width: 100%; padding: 10px 12px; font-size: 16px;
            border: 1.5px solid var(--borde); border-radius: 8px;
            background: #fbfcfe; outline: none;
        }
        .modal input:focus, .modal select:focus, .modal textarea:focus {
            border-color: var(--azul); background: var(--blanco);
            box-shadow: 0 0 0 3px rgba(11,94,168,.12);
        }
        .modal select.deshabilitado {
            background: #eceff3; color: #7a8794;
            pointer-events: none; cursor: not-allowed;
        }
        .modal .pie {
            display: flex; justify-content: flex-end; gap: 8px;
            padding: 12px 18px; border-top: 1px solid var(--borde);
        }
        .modal .error {
            color: #b3261e; font-size: .85rem; min-height: 1.2em;
            margin-top: 10px;
        }
        .cabecera .acceder {
    margin-left: auto;
    color: #ffffff;
    text-decoration: none;
    font-size: .85rem;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 8px;
    background: rgba(255, 255, 255, .15);
    border: 1px solid rgba(255, 255, 255, .25);
    white-space: nowrap;
    transition: background .15s, transform .08s;
    -webkit-tap-highlight-color: transparent;
}

.cabecera .acceder:visited,
.cabecera .acceder:hover,
.cabecera .acceder:active {
    color: #ffffff;
}

.cabecera .acceder:hover {
    background: rgba(255, 255, 255, .25);
}

.cabecera .acceder:active {
    transform: scale(.97);
    background: rgba(255, 255, 255, .3);
}
        @media (max-width: 700px) {
            table.prestamos thead { display: none; }
            table.prestamos, table.prestamos tbody,
            table.prestamos tr, table.prestamos td { display: block; width: 100%; }
            table.prestamos tr { padding: 10px 0; border-bottom: 1px solid var(--borde); }
            table.prestamos td { border: none; padding: 4px 14px; }
            table.prestamos td::before {
                content: attr(data-label) ": ";
                font-size: .75rem; color: var(--texto-suave);
                text-transform: uppercase; letter-spacing: .5px;
                display: block;
            }
        }
    </style>
</head>
<body>

<header class="cabecera">
    <div>
        <h1>GAI Almansa · Préstamos</h1>
        <div class="sub">Registro de préstamos entre hospitales</div>
    </div>
    <a class="acceder" href="<?= site_url('LaboController') ?>">Volver al panel</a>
</header>

<main>
    <button class="btn" id="btn-nuevo">+ Nuevo préstamo</button>

    <h2 class="bloque">Pendientes de devolución (<?= count($sinDevolver) ?>)</h2>
    <table class="prestamos">
        <colgroup>
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 22%;">
          <col style="width: 7%;">
          <col style="width: 11%;">
          <col style="width: 20%;">
          <col style="width: 20%;">
        </colgroup>
        <thead>
            <tr>
                <th>Origen</th><th>Destino</th><th>Artículo</th>
                <th>Uds.</th><th>F. préstamo</th><th>Notas</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sinDevolver as $p): ?>
            <?php
                $clase = (int)$p['origen'] === $almansaId
                    ? 'origen-almansa'
                    : 'destino-almansa';
            ?>
            <tr class="<?= $clase ?>"
                data-id="<?= (int)$p['id_prestamo'] ?>"
                data-origen="<?= (int)$p['origen'] ?>"
                data-destino="<?= (int)$p['destino'] ?>"
                data-articulo="<?= esc($p['articulo'], 'attr') ?>"
                data-unidades="<?= (int)$p['unidades'] ?>"
                data-fecha="<?= esc($p['fecha_prestamo'] ?? '', 'attr') ?>"
                data-notas="<?= esc($p['notas'] ?? '', 'attr') ?>">
                <td data-label="Origen"><?= esc($p['origen_nombre']) ?></td>
                <td data-label="Destino"><?= esc($p['destino_nombre']) ?></td>
                <td data-label="Artículo"><?= esc($p['articulo']) ?></td>
                <td data-label="Unidades"><?= (int)$p['unidades'] ?></td>
                <td data-label="Fecha préstamo"><?= esc($p['fecha_prestamo']) ?></td>
                <td data-label="Notas"><?= esc(mb_strimwidth((string)$p['notas'], 0, 60, '…')) ?></td>
                <td class="acciones">
                    <button class="btn editar">Modificar</button>
                    <button class="btn devolver">Devolver</button>
                    <button class="btn peligro eliminar">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="bloque">Últimos 50 devueltos</h2>
    <table class="prestamos">

        <colgroup>
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 22%;">
          <col style="width: 7%;">
          <col style="width: 11%;">
          <col style="width: 19%;">
          <col style="width: 21%;">
        </colgroup>
        <thead>
            <tr>
                <th>Origen</th><th>Destino</th><th>Artículo</th>
                <th>Uds.</th><th>F. préstamo</th><th>F. devolución</th><th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($devueltos as $p): ?>
            <?php
                $clase = (int)$p['origen'] === $almansaId
                    ? 'origen-almansa'
                    : 'destino-almansa';
            ?>
            <tr class="<?= $clase ?>"
                data-id="<?= (int)$p['id_prestamo'] ?>"
                data-origen="<?= (int)$p['origen'] ?>"
                data-destino="<?= (int)$p['destino'] ?>"
                data-articulo="<?= esc($p['articulo'], 'attr') ?>"
                data-unidades="<?= (int)$p['unidades'] ?>"
                data-fecha="<?= esc($p['fecha_prestamo'] ?? '', 'attr') ?>"
                data-notas="<?= esc($p['notas'] ?? '', 'attr') ?>">
                <td data-label="Origen"><?= esc($p['origen_nombre']) ?></td>
                <td data-label="Destino"><?= esc($p['destino_nombre']) ?></td>
                <td data-label="Artículo"><?= esc($p['articulo']) ?></td>
                <td data-label="Unidades"><?= (int)$p['unidades'] ?></td>
                <td data-label="Fecha préstamo"><?= esc($p['fecha_prestamo']) ?></td>
                <td data-label="Fecha devolución"><?= esc($p['fecha_dev']) ?></td>
                <td class="acciones">
                    <button class="btn editar">Editar</button>
                    <button class="btn anular">Anular devolución</button>
                    <button class="btn peligro eliminar">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- Modal edición / alta -->
<div class="modal-fondo" id="modal">
    <div class="modal">
        <h3 id="modal-titulo">Préstamo</h3>
        <div class="cuerpo">
            <input type="hidden" id="m-id">

            <label for="m-origen">Origen</label>
            <select id="m-origen">
                <option value="">Selecciona hospital</option>
                <?php foreach ($hospitales as $h): ?>
                    <option value="<?= (int)$h['id_hospital'] ?>"><?= esc($h['hospital']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="m-destino">Destino</label>
            <select id="m-destino">
                <option value="">Selecciona hospital</option>
                <?php foreach ($hospitales as $h): ?>
                    <option value="<?= (int)$h['id_hospital'] ?>"><?= esc($h['hospital']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="m-articulo">Artículo</label>
            <input type="text" id="m-articulo">

            <label for="m-unidades">Unidades</label>
            <input type="number" id="m-unidades" min="1">

            <label for="m-fecha">Fecha de préstamo</label>
            <input type="date" id="m-fecha">

            <label for="m-notas">Notas</label>
            <textarea id="m-notas" rows="3"></textarea>

            <div class="error" id="m-error"></div>
        </div>
        <div class="pie">
            <button class="btn" id="m-cancelar">Cancelar</button>
            <button class="btn" id="m-guardar">Guardar</button>
        </div>
    </div>
</div>

<script>
const ALMANSA_ID = <?= (int)$almansaId ?>;
const URL_BASE   = '<?= site_url('prestamos') ?>';

const modal     = document.getElementById('modal');
const mId       = document.getElementById('m-id');
const mOrigen   = document.getElementById('m-origen');
const mDestino  = document.getElementById('m-destino');
const mArticulo = document.getElementById('m-articulo');
const mUnidades = document.getElementById('m-unidades');
const mFecha    = document.getElementById('m-fecha');
const mNotas    = document.getElementById('m-notas');
const mError    = document.getElementById('m-error');
const mTitulo   = document.getElementById('modal-titulo');

function abrirModal(datos = null) {
    mError.textContent = '';
    mOrigen.classList.remove('deshabilitado');
    mDestino.classList.remove('deshabilitado');

    if (datos) {
        mTitulo.textContent = 'Editar préstamo';
        mId.value       = datos.id;
        mOrigen.value   = datos.origen;
        mDestino.value  = datos.destino;
        mArticulo.value = datos.articulo;
        mUnidades.value = datos.unidades;
        mFecha.value    = datos.fecha;
        mNotas.value    = datos.notas;
    } else {
        mTitulo.textContent = 'Nuevo préstamo';
        mId.value = '';
        mOrigen.value = '';
        mDestino.value = '';
        mArticulo.value = '';
        mUnidades.value = 1;
        const hoy = new Date();
        const yyyy = hoy.getFullYear();
        const mm   = String(hoy.getMonth() + 1).padStart(2, '0');
        const dd   = String(hoy.getDate()).padStart(2, '0');
        mFecha.value = `${yyyy}-${mm}-${dd}`;
        mNotas.value = '';
    }

    aplicarRegla('origen');
    aplicarRegla('destino');
    modal.classList.add('abierto');
}

function cerrarModal() {
    modal.classList.remove('abierto');
}

function aplicarRegla(cualCambio) {
    const origen  = parseInt(mOrigen.value || 0, 10);
    const destino = parseInt(mDestino.value || 0, 10);

    // Habilitar y limpiar estados previos
    mOrigen.classList.remove('deshabilitado');
    mDestino.classList.remove('deshabilitado');

    if (cualCambio === 'origen') {
        if (origen && origen !== ALMANSA_ID) {
            mDestino.value = ALMANSA_ID;
            mDestino.classList.add('deshabilitado');
        }
    } else if (cualCambio === 'destino') {
        if (destino && destino !== ALMANSA_ID) {
            mOrigen.value = ALMANSA_ID;
            mOrigen.classList.add('deshabilitado');
        }
    }

    // Deshabilitar la opción Almansa en el select contrario
    const origenActual  = parseInt(mOrigen.value || 0, 10);
    const destinoActual = parseInt(mDestino.value || 0, 10);

    for (const opt of mOrigen.options) {
        opt.disabled = (parseInt(opt.value, 10) === ALMANSA_ID && destinoActual === ALMANSA_ID);
    }
    for (const opt of mDestino.options) {
        opt.disabled = (parseInt(opt.value, 10) === ALMANSA_ID && origenActual === ALMANSA_ID);
    }
}

mOrigen.addEventListener('change',  () => aplicarRegla('origen'));
mDestino.addEventListener('change', () => aplicarRegla('destino'));

document.getElementById('btn-nuevo').addEventListener('click', () => abrirModal());

document.querySelectorAll('.editar').forEach(b => {
    b.addEventListener('click', () => {
        const tr = b.closest('tr');
        abrirModal({
            id:       tr.dataset.id,
            origen:   tr.dataset.origen,
            destino:  tr.dataset.destino,
            articulo: tr.dataset.articulo,
            unidades: tr.dataset.unidades,
            fecha:    tr.dataset.fecha,
            notas:    tr.dataset.notas,
        });
    });
});

document.querySelectorAll('.eliminar').forEach(b => {
    b.addEventListener('click', async () => {
        if (! confirm('¿Seguro que quieres eliminar este préstamo?')) return;
        const tr = b.closest('tr');
        const id = tr.dataset.id;
        const res = await fetch(`${URL_BASE}/eliminar/${id}`, { method: 'POST' });
        if (res.ok) location.reload();
    });
});

document.querySelectorAll('.devolver').forEach(b => {
    b.addEventListener('click', async () => {
        if (! confirm('¿Registrar la devolución de este préstamo?')) return;
        const tr = b.closest('tr');
        const id = tr.dataset.id;
        const res = await fetch(`${URL_BASE}/devolver/${id}`, { method: 'POST' });
        if (res.ok) location.reload();
    });
});

document.getElementById('m-cancelar').addEventListener('click', cerrarModal);
modal.addEventListener('click', e => { if (e.target === modal) cerrarModal(); });

document.querySelectorAll('.anular').forEach(b => {
    b.addEventListener('click', async () => {
        if (! confirm('¿Anular la devolución de este préstamo? Volverá al listado de pendientes.')) return;
        const tr = b.closest('tr');
        const id = tr.dataset.id;
        const res = await fetch(`${URL_BASE}/anular-devolucion/${id}`, { method: 'POST' });
        if (res.ok) location.reload();
    });
});

document.getElementById('m-guardar').addEventListener('click', async () => {
    mError.textContent = '';
    const datos = new URLSearchParams({
        id_prestamo:    mId.value,
        origen:         mOrigen.value,
        destino:        mDestino.value,
        articulo:       mArticulo.value,
        unidades:       mUnidades.value,
        fecha_prestamo: mFecha.value,
        notas:          mNotas.value,
    });

    const res = await fetch(`${URL_BASE}/guardar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: datos.toString(),
    });

    if (res.ok) {
        location.reload();
        return;
    }

    try {
        const err = await res.json();
        mError.textContent = err.error || 'Error al guardar.';
    } catch {
        mError.textContent = 'Error al guardar.';
    }
});
</script>
</body>
</html>