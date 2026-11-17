<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="page-title">Bengkel</h1>
        <p class="page-subtitle">Daftar bengkel yang menangani service kendaraan.</p>
    </div>
    <a href="<?= site_url('bengkel/create') ?>" class="btn btn-brand btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </a>
</div>

<form class="mb-3" action="<?= site_url('bengkel') ?>" method="get">
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" class="form-control" name="q" placeholder="Cari nama atau alamat bengkel..." value="<?= esc($q) ?>">
        <?php if ($q !== ''): ?>
            <a class="input-group-text text-decoration-none" href="<?= site_url('bengkel') ?>" title="Bersihkan">
                <i class="bi bi-x-lg"></i>
            </a>
        <?php endif; ?>
    </div>
</form>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <i class="bi bi-building"></i>
        Belum ada data bengkel.<br>
        <a href="<?= site_url('bengkel/create') ?>" class="btn btn-brand btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Bengkel
        </a>
    </div>
<?php else: ?>
    <div class="card mb-3">
        <?php foreach ($rows as $b): ?>
            <div class="list-row">
                <span class="thumb">
                    <?php if ($b->foto && is_file(FCPATH . 'uploads/bengkel/' . basename($b->foto))): ?>
                        <img src="<?= base_url('uploads/bengkel/' . rawurlencode($b->foto)) ?>" alt="<?= esc($b->nama) ?>">
                    <?php else: ?>
                        <i class="bi bi-building"></i>
                    <?php endif; ?>
                </span>

                <div class="row-main">
                    <div class="row-title"><?= esc($b->nama) ?></div>
                    <div class="row-sub"><i class="bi bi-geo-alt"></i> <?= esc($b->alamat ?? '-') ?></div>
                    <div class="row-sub">
                        <?php if ($b->telepon): ?><i class="bi bi-telephone"></i> <?= esc($b->telepon) ?> &middot;<?php endif; ?>
                        <i class="bi bi-clock"></i> <?= esc($b->jam_buka ?? '-') ?>
                        &middot; <?= $counts[$b->id] ?? 0 ?> service
                    </div>
                    <?php if ($b->layanan): ?>
                        <div class="row-sub mt-1"><i class="bi bi-check2-square"></i> <?= esc($b->layanan) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row-end d-flex flex-column gap-1">
                    <a href="<?= site_url('bengkel/edit/' . $b->id) ?>" class="btn btn-outline-primary btn-icon btn-sm" title="Ubah">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?= site_url('bengkel/delete/' . $b->id) ?>" method="post"
                          data-confirm="Hapus bengkel <?= esc($b->nama) ?>?">
                        <button class="btn btn-outline-danger btn-icon btn-sm" title="Hapus" type="submit">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
