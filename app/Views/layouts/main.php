<?php
/** @var string $content */
$uri  = uri_string();
$part = explode('/', $uri)[0];

$navItems = [
    'dashboard'   => ['url' => 'dashboard',   'icon' => 'bi-house-door', 'label' => 'Beranda'],
    'kendaraan'   => ['url' => 'kendaraan',   'icon' => 'bi-truck',      'label' => 'Kendaraan'],
    'sparepart'   => ['url' => 'sparepart',   'icon' => 'bi-gear',       'label' => 'Spare Part'],
    'bengkel'     => ['url' => 'bengkel',     'icon' => 'bi-building',   'label' => 'Bengkel'],
    'service'     => ['url' => 'service/create', 'icon' => 'bi-wrench',  'label' => 'Input Service'],
    'monitoring'  => ['url' => 'service',     'icon' => 'bi-clipboard-check', 'label' => 'Monitoring'],
    'notifikasi'  => ['url' => 'notifikasi',  'icon' => 'bi-bell',       'label' => 'Notifikasi'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0b4a6f">
    <title><?= esc($title ?? 'Aplikasi') ?> &middot; Monitoring Service</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="app-wrapper">

    <!-- ===== Sidebar (desktop) ===== -->
    <aside class="app-sidebar d-none d-md-flex flex-column">
        <a class="brand" href="<?= site_url('dashboard') ?>">
            <span class="brand-icon"><i class="bi bi-tools"></i></span>
            <span class="brand-text">Service<span class="text-info">Ku</span></span>
        </a>

        <nav class="nav flex-column gap-1 mt-3">
            <?php foreach (['dashboard' => 'dashboard', 'kendaraan' => 'kendaraan', 'sparepart' => 'sparepart', 'bengkel' => 'bengkel'] as $key => $url): ?>
                <a class="nav-link <?= $part === $key || $uri === $url ? 'active' : '' ?>" href="<?= site_url($url) ?>">
                    <i class="bi <?= $navItems[$key]['icon'] ?>"></i> <?= $navItems[$key]['label'] ?>
                </a>
            <?php endforeach; ?>

            <div class="sidebar-divider"></div>

            <a class="nav-link <?= $uri === 'service/create' ? 'active' : '' ?>" href="<?= site_url('service/create') ?>">
                <i class="bi bi-wrench"></i> Input Service
            </a>
            <a class="nav-link <?= $part === 'service' && $uri !== 'service/create' ? 'active' : '' ?>" href="<?= site_url('service') ?>">
                <i class="bi bi-clipboard-check"></i> Monitoring Service
            </a>
            <a class="nav-link <?= $part === 'notifikasi' ? 'active' : '' ?>" href="<?= site_url('notifikasi') ?>">
                <i class="bi bi-bell"></i> Notifikasi
            </a>
        </nav>

        <div class="mt-auto sidebar-footer">
            <div class="sidebar-user">
                <span class="avatar-sm"><i class="bi bi-person"></i></span>
                <div class="small text-truncate">
                    <div class="fw-semibold text-white"><?= esc(session()->get('nama')) ?></div>
                    <div class="text-secondary text-truncate"><?= esc(session()->get('username')) ?></div>
                </div>
            </div>
            <a class="btn btn-outline-light btn-sm w-100 mt-2" href="<?= site_url('logout') ?>">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- ===== Konten ===== -->
    <div class="app-content">

        <!-- Topbar (mobile) -->
        <header class="app-topbar d-md-none">
            <a class="topbar-brand" href="<?= site_url('dashboard') ?>">
                <span class="brand-icon"><i class="bi bi-tools"></i></span>
                <strong>Service<span class="text-info">Ku</span></strong>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-light btn-sm rounded-pill px-3" href="<?= site_url('service/create') ?>">
                    <i class="bi bi-plus-lg"></i> Service
                </a>
            </div>
        </header>

        <main class="app-main">
            <?php if (session()->get('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> <?= esc(session()->get('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->get('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> <?= esc(session()->get('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<!-- ===== Bottom navigation (mobile) ===== -->
<nav class="bottom-nav d-md-none">
    <?php
    $bottom = [
        ['dashboard', 'bi-house-door', 'Beranda'],
        ['kendaraan', 'bi-truck', 'Kendaraan'],
        ['service/create', 'bi-wrench', 'Input'],
        ['service', 'bi-clipboard-check', 'Monitoring'],
        ['menu', 'bi-grid', 'Menu'],
    ];
    foreach ($bottom as [$url, $icon, $label]):
        if ($url === 'service') {
            $active = $part === 'service' && $uri !== 'service/create';
        } elseif ($url === 'service/create') {
            $active = $uri === 'service/create';
        } elseif (strpos($url, '/') === false) {
            $active = $part === $url;
        } else {
            $active = $uri === $url;
        }
    ?>
        <a href="<?= site_url($url) ?>" class="bottom-nav-item <?= $active ? 'active' : '' ?>">
            <i class="bi <?= $icon ?>"></i>
            <span><?= $label ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<script src="<?= base_url('assets/vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
