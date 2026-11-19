<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso</title>
    <style>
        body { font-family: sans-serif; background:#f4f4f4; display:flex;
               align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .box { background:#fff; padding:2rem 2.5rem; border-radius:8px;
               box-shadow:0 4px 16px rgba(0,0,0,.12); width:320px; }
        h1   { font-size:1.2rem; margin-bottom:1.5rem; color:#333; }
        label { display:block; font-size:.85rem; margin-bottom:.3rem; color:#555; }
        input { width:100%; padding:.5rem .75rem; border:1px solid #ccc;
                border-radius:4px; margin-bottom:1rem; box-sizing:border-box; }
        button { width:100%; padding:.6rem; background:#01696f; color:#fff;
                 border:none; border-radius:4px; cursor:pointer; font-size:1rem; }
        button:hover { background:#0c4e54; }
        .error { background:#fde8e8; color:#a12c2c; padding:.5rem .75rem;
                 border-radius:4px; margin-bottom:1rem; font-size:.85rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Acceso</h1>

        <?php if (! empty($error)): ?>
            <div class="error"><?= esc($error) ?></div>
        <?php endif ?>

        <form method="post" action="<?= base_url() ?>">
            <?= csrf_field() ?>

            <label for="alias">Usuario</label>
            <input type="text" id="alias" name="alias"
                autocomplete="username" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password"
                autocomplete="current-password" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>