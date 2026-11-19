<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= esc($fila['prueba']) ?> · GAI Almansa</title>
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
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            overflow-x: hidden;
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

        .boton-volver {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--blanco);
            color: var(--azul);
            border: 1.5px solid var(--borde);
            border-radius: 10px;
            padding: 12px 18px;
            font-size: .95rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: var(--sombra);
            margin-bottom: 18px;
            transition: border-color .15s, background .15s, transform .08s;
            -webkit-tap-highlight-color: transparent;
        }

        .boton-volver:active {
            transform: scale(.98);
            background: var(--verde-suave);
            border-color: var(--verde);
        }

        .boton-volver::before {
            content: "←";
            font-size: 1.1rem;
            line-height: 1;
        }

        .tarjeta {
            background: var(--blanco);
            border-radius: var(--radio);
            box-shadow: var(--sombra);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .titulo-prueba {
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-oscuro) 100%);
            color: var(--blanco);
            margin: 0;
            padding: 18px 18px;
            font-size: 1.15rem;
            font-weight: 600;
            letter-spacing: .2px;
            word-break: break-word;
        }

        table.datos {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.datos tr {
            border-bottom: 1px solid var(--borde);
        }

        table.datos tr:last-child {
            border-bottom: none;
        }

        table.datos th,
        table.datos td {
            padding: 12px 16px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: anywhere;
            hyphens: auto;
        }

        table.datos th {
            background: #f7fafc;
            color: var(--texto-suave);
            font-size: .82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            width: 38%;
            border-right: 1px solid var(--borde);
        }

        table.datos td {
            font-size: .95rem;
            color: var(--texto);
            white-space: pre-line;
        }

        .pie {
            text-align: center;
            margin-top: 28px;
            font-size: .8rem;
            color: var(--texto-suave);
        }

        @media (max-width: 560px) {
            table.datos,
            table.datos tbody,
            table.datos tr,
            table.datos th,
            table.datos td {
                display: block;
                width: 100%;
            }

            table.datos th {
                border-right: none;
                border-bottom: 1px solid var(--borde);
                padding-bottom: 6px;
            }

            table.datos td {
                padding-top: 8px;
                padding-bottom: 14px;
            }
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
            table.datos th { background: #101923; }
            table.datos td { color: #e6edf3; }
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

<<header class="cabecera">
    <img src="<?= base_url('img/logo.png') ?>" alt="" onerror="this.style.display='none'">
    <div>
        <h1>GAI Almansa · Laboratorio</h1>
        <div class="sub"><?= isset($subtitulo) ? esc($subtitulo) : 'Busca una prueba del catálogo' ?></div>
    </div>
    <a class="acceder" href="<?= site_url('LaboController') ?>">Acceder</a>
</header>
<main>
    <a class="boton-volver" href="<?= site_url('/') ?>">Volver a la búsqueda</a>

    <article class="tarjeta">
        <h2 class="titulo-prueba"><?= esc($fila['prueba']) ?></h2>

        <table class="datos">
            <tbody>
                <?php foreach ($etiquetas as $campo => $etiqueta): ?>
                    <?php
                        $valor = $fila[$campo] ?? null;
                        $vacio = $valor === null
                              || $valor === ''
                              || $valor === '.'
                              || trim((string)$valor) === '';
                    ?>
                    <?php if (!$vacio): ?>
                        <tr>
                            <th><?= esc($etiqueta) ?></th>
                            <td><?= esc($valor) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </article>

    <a class="boton-volver" href="<?= site_url('/') ?>">Volver a la búsqueda</a>

    <div class="pie">GAI Almansa · Servicio de Laboratorio</div>
</main>

</body>
</html>