<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(! empty($page_title) ? $page_title . ' — Laboratorio' : 'Laboratorio') ?></title>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
        <?php Arifrh\Themes\Themes::renderCSS(); ?>
    <meta name="csrf-token-name"  content="<?= csrf_token() ?>">
    <meta name="csrf-token"       content="<?= csrf_hash() ?>">
</head>
<body>