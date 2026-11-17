<?php $row = $row ?? null; ?>

<a href="<?= site_url('kendaraan') ?>" class="btn btn-link btn-sm px-0 mb-2 text-decoration-none">
    <i class="bi bi-arrow-left"></i> Kembali ke daftar kendaraan
</a>

<h1 class="page-title"><?= esc($title) ?></h1>
<p class="page-subtitle">Data kendaraan digunakan saat input service.</p>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('kendaraan/save') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) ($row->id ?? 0) ?>">

            <div class="mb-3">
                <label class="form-label req">Jenis Kendaraan</label>
                <div class="btn-group w-100" role="group" aria-label="Jenis kendaraan">
                    <input type="radio" class="btn-check" name="jenis" id="jRoda2" value="roda2"
                        <?= old('jenis', $row->jenis ?? 'roda2') === 'roda2' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" for="jRoda2"><i class="bi bi-bicycle me-1"></i> Roda 2</label>

                    <input type="radio" class="btn-check" name="jenis" id="jRoda4" value="roda4"
                        <?= old('jenis', $row->jenis ?? '') === 'roda4' ? 'checked' : '' ?>>
                    <label class="btn btn-outline-primary" for="jRoda4"><i class="bi bi-car-front me-1"></i> Roda 4</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label req" for="merk">Merk</label>
                <input type="text" class="form-control" id="merk" name="merk" maxlength="50"
                       placeholder="Honda, Toyota, Yamaha, dll." value="<?= esc(old('merk', $row->merk ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="tipe">Tipe / Model</label>
                <input type="text" class="form-control" id="tipe" name="tipe" maxlength="80"
                       placeholder="Vario 160, Avanza 1.3 G, ..." value="<?= esc(old('tipe', $row->tipe ?? '')) ?>">
            </div>

            <div class="row g-2">
                <div class="col-6 mb-3">
                    <label class="form-label" for="no_pol">No. Polisi</label>
                    <input type="text" class="form-control text-uppercase" id="no_pol" name="no_pol" maxlength="20"
                           placeholder="B 1234 ABC" value="<?= esc(old('no_pol', $row->no_pol ?? '')) ?>">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label" for="tahun">Tahun</label>
                    <input type="number" class="form-control" id="tahun" name="tahun" min="1970" max="2100"
                           placeholder="2023" value="<?= esc(old('tahun', $row->tahun ?? '')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="warna">Warna</label>
                <input type="text" class="form-control" id="warna" name="warna" maxlength="30"
                       placeholder="Putih, Hitam, ..." value="<?= esc(old('warna', $row->warna ?? '')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="foto">Foto Kendaraan <span class="text-muted fw-normal">(opsional, maks 2 MB)</span></label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                <div class="form-text">Format: JPG, PNG, WEBP, GIF.</div>

                <?php if (! empty($row->foto)): ?>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="<?= base_url('uploads/kendaraan/' . rawurlencode($row->foto)) ?>"
                             alt="Foto" style="width:70px;height:70px;object-fit:cover;border-radius:12px;border:1px solid #e5edf3">
                        <span class="small text-muted">Foto saat ini (ganti dengan memilih foto baru).</span>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-save me-1"></i> <?= $row ? 'Simpan Perubahan' : 'Tambah Kendaraan' ?>
            </button>
        </form>
    </div>
</div>
