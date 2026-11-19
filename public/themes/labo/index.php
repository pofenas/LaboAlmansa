<?php
$usuario   = $usuario   ?? null;
$menuItems = $menuItems ?? [];
$pageTitle = $pageTitle ?? '';
?>

<header class="banner">
    <div class="banner__logo">
        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
             aria-label="Laboratorio" xmlns="http://www.w3.org/2000/svg">
            <circle cx="18" cy="18" r="18" fill="#01696f"/>
            <path d="M10 26 L14 14 L18 22 L22 14 L26 26"
                  stroke="#fff" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        </svg>
    </div>
    <div class="banner__title">
        <span class="banner__app"></span>
        <?php if (! empty($pageTitle)): ?>
            <span class="banner__page"><?= esc($pageTitle) ?></span>
        <?php endif ?>
    </div>
    <div class="banner__user">
        <?php if ($usuario !== null && $usuario->idUser > 0): ?>
            <span><?= esc($usuario->nombre) ?></span>
            <a href="<?= site_url('/logout') ?>" class="btn-logout" title="Cerrar sesión">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </a>
        <?php endif ?>
    </div>
</header>

<div class="layout">

    <nav class="sidebar" x-data="{ open: null }">
        <?php foreach ($menuItems as $idx => $item): ?>

            <?php if ($item['tipo'] === 'item'): ?>
                <a href="<?= site_url($item['url']) ?>"
                   class="sidebar__item <?= uri_string() === ltrim($item['url'], '/') ? 'sidebar__item--active' : '' ?>">
                    <?= esc($item['label']) ?>
                </a>

            <?php else: ?>
                <div class="sidebar__group">
                    <button class="sidebar__group-toggle"
                            @click="open = (open === <?= $idx ?>) ? null : <?= $idx ?>"
                            :class="{ 'is-open': open === <?= $idx ?> }">
                        <?= esc($item['label']) ?>
                        <svg class="sidebar__chevron" width="14" height="14"
                             viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="sidebar__subitems"
                         x-show="open === <?= $idx ?>"
                         x-transition:enter="subitems-enter"
                         x-transition:enter-start="subitems-enter-start"
                         x-transition:enter-end="subitems-enter-end"
                         x-transition:leave="subitems-enter"
                         x-transition:leave-start="subitems-enter-end"
                         x-transition:leave-end="subitems-enter-start">
                        <?php foreach ($item['items'] as $hijo): ?>
                            <a href="<?= site_url($hijo['url']) ?>"
                               class="sidebar__subitem <?= uri_string() === ltrim($hijo['url'], '/') ? 'sidebar__item--active' : '' ?>">
                                <?= esc($hijo['label']) ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

        <?php endforeach ?>
    </nav>

    <main class="content">
        <?= $content ?? '' ?>
    </main>

</div>