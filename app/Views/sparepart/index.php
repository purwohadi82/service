<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="page-title">Spare Part</h1>
        <p class="page-subtitle">Kode, nama, harga &amp; foto spare part.</p>
    </div>
    <a href="<?= site_url('sparepart/create') ?>" class="btn btn-brand btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </a>
</div>

<form class="mb-3" action="<?= site_url('sparepart') ?>" method="get">
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" class="form-control" name="q" placeholder="Cari kode atau nama spare part..."
               value="<?= esc($q) ?>">
        <?php if ($q !== ''): ?>
            <a class="input-group-text text-decoration-none" href="<?= site_url('sparepart') ?>" title="Bersihkan">
                <i class="bi bi-x-lg"></i>
            </a>
        <?php endif; ?>
    </div>
</form>

<p class="page-subtitle mb-2"><?= $total ?> data ditemukan</p>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <i class="bi bi-gear-wide-connected"></i>
        Tidak ada spare part yang cocok.<br>
        <a href="<?= site_url('sparepart/create') ?>" class="btn btn-brand btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Tambah Spare Part
        </a>
    </div>
<?php else: ?>
    <div class="card mb-3">
        <?php foreach ($rows as $sp): ?>
            <div class="list-row">
                <span class="thumb">
                    <?php if ($sp->foto && is_file(FCPATH . 'uploads/sparepart/' . basename($sp->foto))): ?>
                        <img src="<?= base_url('uploads/sparepart/' . rawurlencode($sp->foto)) ?>" alt="<?= esc($sp->nama) ?>">
                    <?php else: ?>
                        <i class="bi bi-gear"></i>
                    <?php endif; ?>
                </span>

                <div class="row-main">
                    <div class="row-title"><?= esc($sp->nama) ?></div>
                    <div class="row-sub">
                        <span class="badge bg-light text-dark border"><?= esc($sp->kode) ?></span>
                        <span class="ms-1">Stok: <strong><?= (int) $sp->stok ?></strong> <?= esc($sp->satuan) ?></span>
                    </div>
                    <div class="fw-bold mt-1" style="color:#0b4a6f"><?= rupiah($sp->harga) ?></div>
                </div>

                <div class="row-end d-flex flex-column gap-1">
                    <a href="<?= site_url('sparepart/edit/' . $sp->id) ?>" class="btn btn-outline-primary btn-icon btn-sm" title="Ubah">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="<?= site_url('sparepart/delete/' . $sp->id) ?>" method="post"
                          data-confirm="Hapus spare part <?= esc($sp->nama) ?>?">
                        <button class="btn btn-outline-danger btn-icon btn-sm" title="Hapus" type="submit">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
