<h1 class="page-title">Menu</h1>
<p class="page-subtitle">Semua fitur aplikasi dalam satu halaman.</p>

<div class="card mb-3">
    <div class="list-row">
        <span class="thumb"><i class="bi bi-person-circle"></i></span>
        <div class="row-main">
            <div class="row-title"><?= esc(session()->get('nama')) ?></div>
            <div class="row-sub">@<?= esc(session()->get('username')) ?> &middot; <?= esc(session()->get('role')) ?></div>
        </div>
        <div class="row-end">
            <span class="badge bg-success">Online</span>
        </div>
    </div>
</div>

<div class="section-title">Operasional</div>
<div class="quick-grid mb-3">
    <a class="quick-item" href="<?= site_url('service/create') ?>"><i class="bi bi-wrench"></i>Input Service</a>
    <a class="quick-item" href="<?= site_url('service') ?>"><i class="bi bi-clipboard-check"></i>Monitoring</a>
    <a class="quick-item" href="<?= site_url('kendaraan') ?>"><i class="bi bi-truck"></i>Kendaraan</a>
    <a class="quick-item" href="<?= site_url('notifikasi') ?>"><i class="bi bi-bell"></i>Notifikasi</a>
</div>

<div class="section-title">Master Data</div>
<div class="quick-grid mb-3">
    <a class="quick-item" href="<?= site_url('sparepart') ?>"><i class="bi bi-gear"></i>Spare Part</a>
    <a class="quick-item" href="<?= site_url('bengkel') ?>"><i class="bi bi-building"></i>Bengkel</a>
    <a class="quick-item" href="<?= site_url('dashboard') ?>"><i class="bi bi-house-door"></i>Beranda</a>
</div>

<div class="card">
    <div class="p-3">
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger w-100" data-confirm="Yakin ingin keluar dari aplikasi?">
            <i class="bi bi-box-arrow-right me-1"></i> Keluar
        </a>
    </div>
</div>
