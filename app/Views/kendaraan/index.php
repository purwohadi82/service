<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="page-title">Kendaraan</h1>
        <p class="page-subtitle">Kelola kendaraan roda 2 &amp; roda 4 Anda.</p>
    </div>
    <a href="<?= site_url('kendaraan/create') ?>" class="btn btn-brand btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </a>
</div>

<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#0b4a6f,#1a8fc4)"><i class="bi bi-bicycle"></i></span>
            <div>
                <div class="stat-num"><?= $roda2 ?></div>
                <div class="stat-label">Roda 2</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#ff8a00,#ff5e00)"><i class="bi bi-car-front"></i></span>
            <div>
                <div class="stat-num"><?= $roda4 ?></div>
                <div class="stat-label">Roda 4</div>
            </div>
        </div>
    </div>
</div>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <i class="bi bi-truck"></i>
        Belum ada data kendaraan.<br>
        <a href="<?= site_url('kendaraan/create') ?>" class="btn btn-brand btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kendaraan
        </a>
    </div>
<?php else: ?>
    <?php foreach ($rows as $k): ?>
        <div class="card mb-2">
            <div class="list-row">
                <span class="thumb lg">
                    <?php if ($k->foto && is_file(FCPATH . 'uploads/kendaraan/' . basename($k->foto))): ?>
                        <img src="<?= base_url('uploads/kendaraan/' . rawurlencode($k->foto)) ?>" alt="<?= esc($k->merk) ?>">
                    <?php else: ?>
                        <i class="bi <?= $k->jenis === 'roda4' ? 'bi-car-front' : 'bi-bicycle' ?>"></i>
                    <?php endif; ?>
                </span>

                <div class="row-main">
                    <div class="row-title"><?= esc($k->merk . ' ' . ($k->tipe ?? '')) ?></div>
                    <div class="row-sub">
                        <span class="badge <?= $k->jenis === 'roda4' ? 'bg-warning text-dark' : 'bg-info text-dark' ?>">
                            <?= $k->jenis === 'roda4' ? 'Roda 4' : 'Roda 2' ?>
                        </span>
                        <?php if ($k->no_pol): ?><span class="ms-1 fw-semibold"><?= esc($k->no_pol) ?></span><?php endif; ?>
                    </div>
                    <div class="row-sub mt-1">
                        <?= esc($k->warna ?? '-') ?> &middot; <?= $k->tahun ? esc($k->tahun) : '-' ?> &middot;
                        <i class="bi bi-clipboard-check"></i> <?= $counts[$k->id] ?? 0 ?> service
                    </div>
                </div>

                <div class="row-end d-flex flex-column gap-1">
                    <a href="<?= site_url('kendaraan/edit/' . $k->id) ?>" class="btn btn-outline-primary btn-icon btn-sm" title="Ubah">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?= site_url('kendaraan/delete/' . $k->id) ?>" method="post"
                          data-confirm="Hapus kendaraan <?= esc($k->merk) ?> beserta riwayat service-nya?">
                        <button class="btn btn-outline-danger btn-icon btn-sm" title="Hapus" type="submit">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
