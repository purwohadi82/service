<?php
$terakhir = array_slice($terakhir, 0, 5);
$jam  = (int) date('H');
$titip = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 19 ? 'Selamat sore' : 'Selamat malam'));
?>

<div class="hero mb-3">
    <h5><?= $titip ?>, <?= esc(explode(' ', session()->get('nama'))[0]) ?> 👋</h5>
    <div class="hero-value"><?= rupiah($totalBiaya) ?></div>
    <div class="hero-meta">Total biaya service dari <?= $jumlahService ?> catatan service</div>

    <?php if ($jumlahPengingat > 0): ?>
        <a href="<?= site_url('notifikasi') ?>" class="btn btn-light btn-sm mt-3 position-relative">
            <i class="bi bi-bell-fill text-danger me-1"></i> <?= $jumlahPengingat ?> pengingat perlu diperhatikan
        </a>
    <?php endif; ?>
</div>

<div class="row g-2 mb-3">
    <div class="col-4">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#0b4a6f,#1a8fc4)"><i class="bi bi-truck"></i></span>
            <div>
                <div class="stat-num"><?= $jumlahKendaraan ?></div>
                <div class="stat-label">Kendaraan</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#ff8a00,#ff5e00)"><i class="bi bi-wrench"></i></span>
            <div>
                <div class="stat-num"><?= $jumlahService ?></div>
                <div class="stat-label">Total Service</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#198754,#20c997)"><i class="bi bi-calendar-check"></i></span>
            <div>
                <div class="stat-num"><?= $serviceBulanIni ?></div>
                <div class="stat-label">Bulan Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="section-title">Menu Cepat</div>
<div class="quick-grid">
    <a class="quick-item" href="<?= site_url('service/create') ?>"><i class="bi bi-wrench"></i>Input Service</a>
    <a class="quick-item" href="<?= site_url('service') ?>"><i class="bi bi-clipboard-check"></i>Monitoring</a>
    <a class="quick-item" href="<?= site_url('kendaraan') ?>"><i class="bi bi-truck"></i>Kendaraan</a>
    <a class="quick-item" href="<?= site_url('sparepart') ?>"><i class="bi bi-gear"></i>Spare Part</a>
    <a class="quick-item" href="<?= site_url('bengkel') ?>"><i class="bi bi-building"></i>Bengkel</a>
    <a class="quick-item" href="<?= site_url('notifikasi') ?>"><i class="bi bi-bell"></i>Notifikasi</a>
    <a class="quick-item" href="<?= site_url('menu') ?>"><i class="bi bi-grid"></i>Menu Lain</a>
    <a class="quick-item" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i>Keluar</a>
</div>

<?php if ($pengingat !== []): ?>
    <div class="section-title">Pengingat Service Berikutnya</div>
    <div class="card mb-3">
        <?php foreach ($pengingat as $p): $badge = status_jatuh_tempo($p->jatuh_tempo); ?>
            <div class="list-row">
                <span class="thumb"><i class="bi <?= $p->kendaraan->jenis === 'roda4' ? 'bi-car-front' : 'bi-bicycle' ?>"></i></span>
                <div class="row-main">
                    <div class="row-title"><?= esc($p->kendaraan->merk . ' ' . ($p->kendaraan->tipe ?? '')) ?></div>
                    <div class="row-sub">
                        <i class="bi bi-calendar-event"></i> <?= tanggal_id($p->jatuh_tempo) ?>
                        &middot; <?= esc($p->sumber ?? 'belum ada riwayat') ?>
                    </div>
                </div>
                <div class="row-end">
                    <?php if ($badge !== null): ?><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="section-title">Service Terakhir</div>

<?php if ($terakhir === []): ?>
    <div class="empty-state">
        <i class="bi bi-clipboard-x"></i>
        Belum ada data service.<br>
        <a href="<?= site_url('service/create') ?>" class="btn btn-brand btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Input Service Sekarang
        </a>
    </div>
<?php else: ?>
    <div class="card mb-3">
        <?php foreach ($terakhir as $s): ?>
            <a class="list-row" href="<?= site_url('service/detail/' . $s->id) ?>">
                <span class="thumb"><i class="bi <?= $s->jenis === 'roda4' ? 'bi-car-front' : 'bi-bicycle' ?>"></i></span>
                <div class="row-main">
                    <div class="row-title"><?= esc($s->merk . ' ' . ($s->tipe ?? '')) ?></div>
                    <div class="row-sub"><?= tanggal_id($s->tanggal) ?> &middot; <?= esc($s->nama_bengkel ?? '-') ?></div>
                </div>
                <div class="row-end">
                    <div class="fw-bold" style="color:#0b4a6f;font-size:.9rem"><?= rupiah($s->total_biaya) ?></div>
                    <?= badge_status($s->status) ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <a href="<?= site_url('service') ?>" class="btn btn-outline-secondary btn-sm w-100">
        Lihat semua riwayat <i class="bi bi-arrow-right"></i>
    </a>
<?php endif; ?>
