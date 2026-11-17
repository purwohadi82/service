<?php $row = $row ?? null; ?>

<a href="<?= site_url('bengkel') ?>" class="btn btn-link btn-sm px-0 mb-2 text-decoration-none">
    <i class="bi bi-arrow-left"></i> Kembali ke daftar bengkel
</a>

<h1 class="page-title"><?= esc($title) ?></h1>
<p class="page-subtitle">Data bengkel dipakai pada form input service.</p>

<div class="card">
    <div class="card-body">
        <form action="<?= site_url('bengkel/save') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= (int) ($row->id ?? 0) ?>">

            <div class="mb-3">
                <label class="form-label req" for="nama">Nama Bengkel</label>
                <input type="text" class="form-control" id="nama" name="nama" maxlength="120"
                       placeholder="Bengkel Jaya Motor" value="<?= esc(old('nama', $row->nama ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="2" maxlength="255"
                          placeholder="Jl. Merdeka No. 12, Jakarta"><?= esc(old('alamat', $row->alamat ?? '')) ?></textarea>
            </div>

            <div class="row g-2">
                <div class="col-6 mb-3">
                    <label class="form-label" for="telepon">Telepon</label>
                    <input type="text" class="form-control" id="telepon" name="telepon" maxlength="30"
                           placeholder="021-5551234" value="<?= esc(old('telepon', $row->telepon ?? '')) ?>">
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label" for="jam_buka">Jam Buka</label>
                    <input type="text" class="form-control" id="jam_buka" name="jam_buka" maxlength="60"
                           placeholder="Senin - Sabtu (08.00 - 17.00)" value="<?= esc(old('jam_buka', $row->jam_buka ?? '')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="layanan">Layanan</label>
                <input type="text" class="form-control" id="layanan" name="layanan" maxlength="255"
                       placeholder="Servis rutin, ganti oli, ban, rem..." value="<?= esc(old('layanan', $row->layanan ?? '')) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="foto">Foto Bengkel <span class="text-muted fw-normal">(opsional, maks 2 MB)</span></label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">

                <?php if (! empty($row->foto)): ?>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="<?= base_url('uploads/bengkel/' . rawurlencode($row->foto)) ?>"
                             alt="Foto" style="width:70px;height:70px;object-fit:cover;border-radius:12px;border:1px solid #e5edf3">
                        <span class="small text-muted">Foto saat ini.</span>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-save me-1"></i> <?= $row ? 'Simpan Perubahan' : 'Tambah Bengkel' ?>
            </button>
        </form>
    </div>
</div>
