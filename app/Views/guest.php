<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>GAI Almansa - Laboratorio</title>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <style>
        :root {
            --azul:        #0b5ea8;
            --azul-oscuro: #07437a;
            --verde:       #1f9d7a;
            --verde-suave: #e8f5f0;
            --fondo:       #f4f7fa;
            --texto:       #1c2b36;
            --texto-suave: #5a6b78;
            --borde:       #dbe3ea;
            --blanco:      #ffffff;
            --sombra:      0 1px 3px rgba(16, 40, 60, .08),
                           0 4px 12px rgba(16, 40, 60, .05);
            --radio:       12px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--fondo);
            color: var(--texto);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 16px;
            line-height: 1.45;
            -webkit-text-size-adjust: 100%;
        }

        .cabecera {
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
            color: var(--blanco);
            padding: 14px 18px;
            padding-top: max(14px, env(safe-area-inset-top));
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .cabecera img {
            height: 36px;
            width: auto;
            display: block;
            background: rgba(255,255,255,.15);
            border-radius: 8px;
            padding: 4px;
        }

        .cabecera h1 {
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: .2px;
        }

        .cabecera .sub {
            font-size: .78rem;
            opacity: .85;
            margin-top: 2px;
        }

        main {
            max-width: 720px;
            margin: 0 auto;
            padding: 18px 16px 40px;
        }

        .buscador {
            background: var(--blanco);
            border-radius: var(--radio);
            padding: 16px;
            box-shadow: var(--sombra);
            margin-bottom: 16px;
        }

        .buscador label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: var(--texto-suave);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .buscador input {
            width: 100%;
            font-size: 16px;
            padding: 14px 14px;
            border: 1.5px solid var(--borde);
            border-radius: 10px;
            outline: none;
            background: #fbfcfe;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .buscador input:focus {
            border-color: var(--azul);
            background: var(--blanco);
            box-shadow: 0 0 0 4px rgba(11, 94, 168, .12);
        }

        .buscador .ayuda {
            font-size: .82rem;
            color: var(--texto-suave);
            margin-top: 8px;
            min-height: 1.1em;
        }

        .estado {
            font-size: .9rem;
            color: var(--texto-suave);
            text-align: center;
            padding: 10px 0;
            min-height: 1.2em;
        }

        .estado.error { color: #b3261e; }

        .contador {
            font-size: .82rem;
            color: var(--texto-suave);
            margin: 4px 2px 8px;
        }

        .resultados {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .resultados li a {
            display: block;
            background: var(--blanco);
            border: 1px solid var(--borde);
            border-radius: var(--radio);
            padding: 14px 16px;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--sombra);
            transition: transform .08s, border-color .15s, background .15s;
            -webkit-tap-highlight-color: transparent;
        }

        .resultados li a:active {
            transform: scale(.985);
            background: var(--verde-suave);
            border-color: var(--verde);
        }

        .resultados li a:focus-visible {
            outline: 3px solid rgba(11, 94, 168, .35);
            outline-offset: 2px;
        }

        .prueba-nombre {
            font-weight: 600;
            font-size: 1rem;
            color: var(--azul-oscuro);
            word-break: break-word;
        }

        .prueba-tubo {
            font-size: .85rem;
            color: var(--verde);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .prueba-tubo::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--verde);
            flex: 0 0 auto;
        }

        .pie {
            text-align: center;
            margin-top: 28px;
            font-size: .8rem;
            color: var(--texto-suave);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --fondo:       #0f1720;
                --texto:       #e6edf3;
                --texto-suave: #9aa9b5;
                --borde:       #26323d;
                --blanco:      #17202a;
                --verde-suave: #123a2e;
                --sombra:      0 1px 3px rgba(0,0,0,.4), 0 4px 12px rgba(0,0,0,.3);
            }
            .buscador input { background: #101923; }
            .prueba-nombre { color: #9ec8f0; }
            .prueba-tubo   { color: #62c9a5; }
            .prueba-tubo::before { background: #62c9a5; }
        }
        .cabecera .acceder {
    margin-left: auto;
    color: var(--blanco);
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

.cabecera .acceder:hover {
    background: rgba(255, 255, 255, .25);
}

.cabecera .acceder:active {
    transform: scale(.97);
    background: rgba(255, 255, 255, .3);
}
    </style>
</head>
<body>

<header class="cabecera">
    <img src="<?= base_url('img/logo.png') ?>" alt="" onerror="this.style.display='none'">
    <div>
        <h1>GAI Almansa · Laboratorio</h1>
        <div class="sub"><?= isset($subtitulo) ? esc($subtitulo) : 'Busca una prueba del catálogo' ?></div>
    </div>
    <a class="acceder" href="<?= site_url('LaboController') ?>">Acceder</a>
</header>

<main>
    <section class="buscador">
        <label for="q">Buscar prueba</label>
        <input id="q" type="text" inputmode="search" autocomplete="off"
               autocapitalize="off" spellcheck="false" autofocus
               placeholder="Ej. glucosa, hemograma, TSH…">
        <div class="ayuda" id="ayuda">Escribe al menos 3 caracteres.</div>
    </section>

    <div class="estado" id="estado"></div>
    <div class="contador" id="contador"></div>
    <ul class="resultados" id="resultados"></ul>

    <div class="pie">GAI Almansa · Servicio de Laboratorio</div>
</main>

<script>
const input    = document.getElementById('q');
const lista    = document.getElementById('resultados');
const estado   = document.getElementById('estado');
const contador = document.getElementById('contador');
const ayuda    = document.getElementById('ayuda');
let timer;

input.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();

    lista.innerHTML = '';
    contador.textContent = '';
    estado.classList.remove('error');
    estado.textContent = '';

    if (q.length < 3) {
        ayuda.textContent = 'Escribe al menos 3 caracteres.';
        return;
    }

    ayuda.textContent = '';
    timer = setTimeout(() => buscar(q), 250);
});

async function buscar(q) {
    estado.classList.remove('error');
    estado.textContent = 'Buscando…';

    try {
        const res = await fetch(`<?= site_url('api/buscar') ?>?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const datos = await res.json();
        estado.textContent = '';

        if (!datos.length) {
            contador.textContent = '';
            estado.textContent = `Sin resultados para «${q}».`;
            return;
        }

        contador.textContent = `${datos.length} resultado${datos.length === 1 ? '' : 's'} para «${q}»`;
        lista.innerHTML = datos.map(p => {
            const tubo = p.tubo && p.tubo !== '.' ? p.tubo : 'Tubo no especificado';
            return `<li>
                <a href="<?= site_url('prueba') ?>/${p.id_prueba}">
                    <div class="prueba-nombre">${escapeHtml(p.prueba)}</div>
                    <div class="prueba-tubo">${escapeHtml(tubo)}</div>
                </a>
            </li>`;
        }).join('');
    } catch (e) {
        estado.classList.add('error');
        estado.textContent = 'Error al buscar. Inténtalo de nuevo.';
    }
}

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[c]));
}
</script>
</body>
</html>