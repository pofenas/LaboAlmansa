<?php
/**
 * Listado de equipos con códigos QR.
 *
 * Variables:
 * - $equipos
 */
?>

<section class="fer-qr-page">

    <header class="fer-qr-header">
        <h1>Códigos QR de equipos</h1>

        <p>
            Cada código abre el formulario de incidencias
            del equipo correspondiente.
        </p>
    </header>

    <?php if (empty($equipos)): ?>

        <div class="fer-qr-empty">
            No hay equipos registrados.
        </div>

    <?php else: ?>

        <div class="fer-qr-grid">

            <?php foreach ($equipos as $equipo): ?>

                <article class="fer-qr-card">

                    <h2 class="fer-qr-name">
                        <?= esc($equipo['nombre']) ?>
                    </h2>

                    <p class="fer-qr-id">
                        Equipo #<?= (int) $equipo['id_equipo'] ?>
                    </p>

                    <img class="fer-qr-image"
                         src="<?= esc($equipo['qr_uri'], 'attr') ?>"
                         alt="<?= esc(
                             'Código QR para reportar incidencias de '
                             . $equipo['nombre'],
                             'attr'
                         ) ?>"
                         width="300"
                         height="300">

                    <p class="fer-qr-instruction">
                        Escanea para reportar una incidencia
                    </p>

                    <p class="fer-qr-url">
                        <?= esc($equipo['fer_url']) ?>
                    </p>

                </article>

            <?php endforeach ?>

        </div>

        <p class="fer-qr-summary">
            Equipos: <?= count($equipos) ?>
        </p>

    <?php endif ?>

</section>

<style>
    .fer-qr-page
    {
        width: 100%;
        min-width: 0;
    }

    .fer-qr-header
    {
        margin-bottom: 1.5rem;
    }

    .fer-qr-header h1
    {
        margin: 0 0 .5rem;
        font-size: 1.5rem;
    }

    .fer-qr-header p
    {
        margin: 0;
        color: #596b71;
    }

    .fer-qr-grid
    {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
    }

    .fer-qr-card
    {
        display: flex;
        flex-direction: column;
        align-items: center;

        min-width: 0;
        padding: 1.25rem;

        background: #fff;
        color: #222;
        border: 1px solid #d7e1e3;
        border-radius: 10px;

        text-align: center;
        break-inside: avoid;
    }

    .fer-qr-name
    {
        margin: 0;
        font-size: 1.1rem;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .fer-qr-id
    {
        margin: .35rem 0 .75rem;
        color: #596b71;
        font-size: .85rem;
    }

    .fer-qr-image
    {
        display: block;
        width: 220px;
        max-width: 100%;
        height: auto;
        background: #fff;
    }

    .fer-qr-instruction
    {
        margin: .75rem 0 .5rem;
        font-size: .9rem;
        font-weight: 600;
    }

    .fer-qr-url
    {
        max-width: 100%;
        margin: 0;

        color: #596b71;
        font-size: .75rem;
        overflow-wrap: anywhere;
    }

    .fer-qr-summary
    {
        margin-top: 1rem;
        color: #596b71;
        font-size: .9rem;
    }

    .fer-qr-empty
    {
        padding: 1.25rem;
        border: 1px solid #d7e1e3;
        border-radius: 8px;
        background: #fff;
        color: #596b71;
    }
</style>