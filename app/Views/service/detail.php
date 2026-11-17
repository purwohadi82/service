<?php
$subtotalParts = 0;
foreach ($items as $i) {
    $subtotalParts += (float) $i->subtotal;
}
$jatuh = $row->next_service_date ? status_jatuh_tempo($row->next_service_date) : null;
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="page-title">Detail Service</h1>
        <p class="page-subtitle">Catatan service #<?= (int) $row->id ?></p>
    </div>
    <a href="<?= site_url('service') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Ringkasan -->
<div class="hero mb-3">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5><?= esc($row->merk . ' ' . ($row->tipe ?? '')) ?></h5>
            <div class="hero-value"><?= rupiah($row->total_biaya) ?></div>
            <div class="hero-meta">
                <?= tanggal_id($row->tanggal) ?>
                <?php if ($row->no_pol): ?> &middot; <?= esc($row->no_pol) ?><?php endif; ?>
                <?php if ($row->odometer): ?> &middot; <?= number_format((int) $row->odometer) ?> km<?php endif; ?>
            </div>
        </div>
        <div style="position:relative;z-index:1"><?= badge_status($row->status) ?></div>
    </div>
</div>

<?php if ($jatuh !== null): ?>
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
        <div>
            <i class="bi bi-bell-fill text-warning me-1"></i>
            <strong>Service berikutnya:</strong> <?= tanggal_id($row->next_service_date) ?>
            <?php if ($row->next_service_odometer): ?>
                <div class="small text-muted">atau odometer <?= number_format((int) $row->next_service_odometer) ?> km</div>
            <?php endif; ?>
        </div>
        <span class="badge <?= $jatuh['class'] ?>"><?= $jatuh['label'] ?></span>
    </div>
<?php endif; ?>

<!-- Informasi -->
<div class="card mb-3">
    <div class="detail-group">
        <div class="detail-label">Bengkel</div>
        <div class="detail-value"><?= esc($row->nama_bengkel ?? 'Tidak ditentukan') ?></div>
        <?php if ($row->alamat_bengkel): ?><div class="small text-muted"><i class="bi bi-geo-alt"></i> <?= esc($row->alamat_bengkel) ?></div><?php endif; ?>
        <?php if ($row->telp_bengkel): ?><div class="small text-muted"><i class="bi bi-telephone"></i> <?= esc($row->telp_bengkel) ?></div><?php endif; ?>
    </div>

    <div class="detail-group">
        <div class="detail-label">Keluhan / Pekerjaan</div>
        <div class="detail-value" style="font-weight:500"><?= nl2br(esc($row->keluhan ?? '-')) ?></div>
    </div>

    <div class="detail-group">
        <div class="row">
            <div class="col-6">
                <div class="detail-label">Teknisi</div>
                <div class="detail-value"><?= esc($row->teknisi ?? '-') ?></div>
            </div>
            <div class="col-6">
                <div class="detail-label">Dicatat</div>
                <div class="detail-value"><?= tanggal_id($row->created_at, true) ?></div>
            </div>
        </div>
    </div>

    <?php if ($row->catatan): ?>
        <div class="detail-group">
            <div class="detail-label">Catatan</div>
            <div class="detail-value" style="font-weight:500"><?= nl2br(esc($row->catatan)) ?></div>
        </div>
    <?php endif; ?>
</div>

<!-- Rincian biaya -->
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-receipt me-1"></i> Rincian Biaya</div>

    <?php if ($items === []): ?>
        <div class="p-3 text-muted small">Tidak ada spare part terpakai.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Spare Part</th>
                        <th class="text-center" style="width:56px">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $i): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($i->nama) ?></div>
                                <div class="small text-muted"><?= rupiah($i->harga_satuan) ?> / pcs</div>
                            </td>
                            <td class="text-center"><?= (int) $i->qty ?></td>
                            <td class="text-end fw-semibold"><?= rupiah($i->subtotal) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end text-muted small">Subtotal spare part</td>
                        <td class="text-end"><?= rupiah($subtotalParts) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end text-muted small">Biaya jasa</td>
                        <td class="text-end"><?= rupiah($row->biaya_jasa) ?></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold" style="color:#0b4a6f"><?= rupiah($row->total_biaya) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Ubah status -->
<div class="card mb-3">
    <div class="card-body">
        <div class="detail-label mb-2">Ubah Status Service</div>
        <form action="<?= site_url('service/status/' . $row->id) ?>" method="post" class="d-flex gap-2">
            <select class="form-select form-select-sm" name="status">
                <?php foreach (['dijadwalkan', 'diproses', 'selesai'] as $st): ?>
                    <option value="<?= $st ?>" <?= $row->status === $st ? 'selected' : '' ?>>
                        <?= ucfirst($st) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-brand btn-sm px-3" type="submit"><i class="bi bi-check-lg"></i> Simpan</button>
        </form>
    </div>
</div>

<!-- Pengingat -->
<?php if ($pengingat !== null): ?>
    <div class="card mb-3">
        <div class="list-row">
            <span class="thumb"><i class="bi bi-bell"></i></span>
            <div class="row-main">
                <div class="row-title">Notifikasi service berikutnya</div>
                <div class="row-sub">
                    <?= $pengingat->aktif ? 'Aktif' : 'Nonaktif' ?> &middot;
                    ingatkan <?= (int) $pengingat->sebelum_hari ?> hari sebelum jatuh tempo
                    &middot; pukul <?= esc(substr((string) $pengingat->jam_pengingat, 0, 5)) ?>
                </div>
            </div>
            <div class="row-end">
                <a href="<?= site_url('notifikasi') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-gear"></i> Atur
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Aksi -->
<div class="d-flex gap-2">
    <a href="<?= site_url('service/create') ?>" class="btn btn-accent flex-fill">
        <i class="bi bi-plus-lg me-1"></i> Input Service Baru
    </a>
    <form action="<?= site_url('service/delete/' . $row->id) ?>" method="post"
          data-confirm="Hapus catatan service ini?">
        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
    </form>
</div>
