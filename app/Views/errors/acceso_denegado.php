<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso denegado</title>
    <style>
        body { font-family:sans-serif; background:#f4f4f4; display:flex;
               align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .box { background:#fff; padding:2.5rem 3rem; border-radius:8px;
               box-shadow:0 4px 16px rgba(0,0,0,.1); text-align:center; max-width:420px; }
        h1   { font-size:3rem; color:#a12c2c; margin:0 0 .5rem; }
        h2   { font-size:1.2rem; color:#333; margin:0 0 1rem; }
        p    { color:#666; font-size:.95rem; }
        a    { color:#01696f; text-decoration:none; font-weight:bold; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="box">
        <h1>403</h1>
        <h2>Acceso denegado</h2>
        <p>
            <?= esc($usuario->nombre ?? 'Usuario') ?>,
            no tienes permiso para acceder a esta sección.
        </p>
        <p><a href="<?= site_url('/') ?>">← Volver al inicio</a></p>
    </div>
</body>
</html>