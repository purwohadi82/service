<?php $row = $row ?? null; ?>

<a href="<?= site_url('sparepart') ?>" class="btn btn-link btn-sm px-0 mb-2 text-decoration-none">
    <i class="bi bi-arrow-left"></i> Kembali ke daftar spare part
</a>

<h1 class="page-title"><?= esc($title) ?></h1>
<p class="page-subtitle">Isi kode, nama, harga, dan foto spare part.</p>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('sparepart/save') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) ($row->id ?? 0) ?>">

            <div class="mb-3">
                <label class="form-label req" for="kode">Kode Spare Part</label>
                <input type="text" class="form-control" id="kode" name="kode" maxlength="30"
                       placeholder="SP-0001" value="<?= esc(old('kode', $row->kode ?? ($nextCode ?? ''))) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label req" for="nama">Nama Spare Part</label>
                <input type="text" class="form-control" id="nama" name="nama" maxlength="120"
                       placeholder="Oli Mesin 10W-40 1L" value="<?= esc(old('nama', $row->nama ?? '')) ?>" required>
            </div>

            <div class="row g-2">
                <div class="col-7 mb-3">
                    <label class="form-label req" for="harga">Harga (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control input-rupiah" id="harga" name="harga"
                               placeholder="65000"
                               value="<?= esc(old('harga', (isset($row->harga) && $row->harga !== '') ? number_format((float) $row->harga, 0, ',', '.') : '')) ?>" required>
                    </div>
                </div>
                <div class="col-5 mb-3">
                    <label class="form-label" for="satuan">Satuan</label>
                    <input type="text" class="form-control" id="satuan" name="satuan" maxlength="20"
                           placeholder="pcs" value="<?= esc(old('satuan', $row->satuan ?? 'pcs')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="stok">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" min="0"
                       value="<?= esc(old('stok', $row->stok ?? 0)) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="foto">Foto Spare Part <span class="text-muted fw-normal">(opsional, maks 2 MB)</span></label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                <div class="form-text">Format: JPG, PNG, WEBP, GIF.</div>

                <?php if (! empty($row->foto)): ?>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="<?= base_url('uploads/sparepart/' . rawurlencode($row->foto)) ?>"
                             alt="Foto" style="width:70px;height:70px;object-fit:cover;border-radius:12px;border:1px solid #e5edf3">
                        <span class="small text-muted">Foto saat ini (ganti dengan memilih foto baru).</span>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-save me-1"></i> <?= $row ? 'Simpan Perubahan' : 'Tambah Spare Part' ?>
            </button>
        </form>
    </div>
</div>
