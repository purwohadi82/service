<?php
/** @var array $kendaraan, $bengkel, $sparepart, $items */

$spJson = array_map(static fn ($s) => [
    'id'    => (int) $s->id,
    'kode'  => $s->kode,
    'nama'  => $s->nama,
    'harga' => (float) $s->harga,
    'foto'  => $s->foto,
], $sparepart);
?>

<a href="<?= site_url('service') ?>" class="btn btn-link btn-sm px-0 mb-2 text-decoration-none">
    <i class="bi bi-arrow-left"></i> Kembali ke monitoring service
</a>

<h1 class="page-title">Input Service Kendaraan</h1>
<p class="page-subtitle">Catat service, spare part terpakai, dan jadwal service berikutnya.</p>

<?php if ($sparepart === []): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Belum ada data spare part. <a href="<?= site_url('sparepart/create') ?>" class="fw-bold">Tambahkan spare part</a> terlebih dahulu.
    </div>
<?php endif; ?>

<form action="<?= site_url('service/save') ?>" method="post" id="formService">
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-truck me-1"></i> Kendaraan &amp; Waktu Service</div>
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label req" for="kendaraan_id">Kendaraan</label>
                <select class="form-select" id="kendaraan_id" name="kendaraan_id" required>
                    <option value="">-- Pilih kendaraan --</option>
                    <?php foreach ($kendaraan as $k): ?>
                        <option value="<?= $k->id ?>" data-jenis="<?= $k->jenis ?>"
                            data-no="<?= esc($k->no_pol ?? '') ?>"
                            data-label="<?= esc($k->merk . ' ' . ($k->tipe ?? '')) ?>">
                            <?= esc($k->merk . ' ' . ($k->tipe ?? '') . ($k->no_pol ? ' - ' . $k->no_pol : '')) ?>
                            (<?= $k->jenis === 'roda4' ? 'Roda 4' : 'Roda 2' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="infoKendaraan" class="form-text"></div>
            </div>

            <div class="row g-2">
                <div class="col-6 mb-3">
                    <label class="form-label req" for="tanggal">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                           value="<?= esc(old('tanggal', date('Y-m-d'))) ?>" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label" for="odometer">Odometer (km)</label>
                    <input type="number" class="form-control" id="odometer" name="odometer" min="0"
                           placeholder="45000" value="<?= esc(old('odometer')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="bengkel_id">Bengkel</label>
                <select class="form-select" id="bengkel_id" name="bengkel_id">
                    <option value="">-- Pilih bengkel (opsional) --</option>
                    <?php foreach ($bengkel as $b): ?>
                        <option value="<?= $b->id ?>" data-telp="<?= esc($b->telepon ?? '') ?>" data-jam="<?= esc($b->jam_buka ?? '') ?>">
                            <?= esc($b->nama) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="infoBengkel" class="form-text"></div>
            </div>

            <div class="mb-0">
                <label class="form-label" for="keluhan">Keluhan / Pekerjaan</label>
                <textarea class="form-control" id="keluhan" name="keluhan" rows="3" maxlength="1000"
                          placeholder="Contoh: bunyi gesek di roda depan, ganti oli rutin, servis 10.000 km..."><?= esc(old('keluhan')) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Spare part terpakai -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-gear me-1"></i> Spare Part Terpakai</span>
            <button type="button" class="btn btn-accent btn-sm" id="btnTambahItem">
                <i class="bi bi-plus-lg"></i> Item
            </button>
        </div>
        <div class="card-body">
            <div id="daftarItem"></div>

            <?php if (empty($items)): ?>
                <div class="form-text mb-2">Belum ada item. Klik <strong>+ Item</strong> untuk memilih spare part.</div>
            <?php endif; ?>

            <div class="row g-2 align-items-end mt-1">
                <div class="col-6">
                    <label class="form-label" for="biaya_jasa">Biaya Jasa (Rp)</label>
                    <input type="text" inputmode="numeric" class="form-control input-rupiah" id="biaya_jasa"
                           name="biaya_jasa" placeholder="0"
                           value="<?= esc(old('biaya_jasa')) ?>">
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-3 text-center" style="background:#e9f3f9">
                        <div style="font-size:.72rem;color:#5b6b7a;font-weight:700">TOTAL BIAYA</div>
                        <div class="fw-bold" id="totalBiaya" style="color:#0b4a6f;font-size:1.25rem">Rp 0</div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="total_biaya" id="totalBiayaInput" value="0">
        </div>
    </div>

    <!-- Status & jadwal berikutnya -->
    <div class="card mb-3">
        <div class="card-header"><i class="bi bi-calendar-week me-1"></i> Status &amp; Service Berikutnya</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label req" for="status">Status Service</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (['selesai' => 'Selesai', 'diproses' => 'Diproses', 'dijadwalkan' => 'Dijadwalkan'] as $val => $lab): ?>
                        <option value="<?= $val ?>" <?= old('status', 'selesai') === $val ? 'selected' : '' ?>><?= $lab ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-2">
                <div class="col-6 mb-3">
                    <label class="form-label" for="next_service_date">Tanggal Service Berikutnya</label>
                    <input type="date" class="form-control" id="next_service_date" name="next_service_date"
                           value="<?= esc(old('next_service_date')) ?>">
                    <div class="form-text">Otomatis disarankan +180 hari.</div>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label" for="next_service_odometer">Odometer Berikutnya (km)</label>
                    <input type="number" class="form-control" id="next_service_odometer" name="next_service_odometer" min="0"
                           placeholder="50000" value="<?= esc(old('next_service_odometer')) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="teknisi">Teknisi</label>
                <input type="text" class="form-control" id="teknisi" name="teknisi" maxlength="100"
                       placeholder="Nama teknisi (opsional)" value="<?= esc(old('teknisi')) ?>">
            </div>

            <div class="mb-0">
                <label class="form-label" for="catatan">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="2" maxlength="1000"
                          placeholder="Garansi, saran, dll."><?= esc(old('catatan')) ?></textarea>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-brand w-100 py-2 mb-2">
        <i class="bi bi-save me-1"></i> Simpan Data Service
    </button>
</form>

<template id="tplItem">
    <div class="sp-row" data-row>
        <div class="d-flex gap-2 align-items-start">
            <div class="flex-grow-1">
                <select class="form-select form-select-sm mb-2" data-sp name="sp_id[]" required>
                    <option value="">-- Pilih spare part --</option>
                </select>
                <div class="d-flex gap-2 align-items-center">
                    <div class="input-group input-group-sm" style="width:96px">
                        <span class="input-group-text">Qty</span>
                        <input type="number" class="form-control" data-qty name="sp_qty[]" min="1" value="1">
                    </div>
                    <div class="small text-muted flex-grow-1" data-harga>@ Rp 0</div>
                    <div class="fw-bold small" data-subtotal style="color:#0b4a6f">Rp 0</div>
                    <button type="button" class="btn btn-outline-danger btn-sm btn-icon" data-hapus title="Hapus item">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
window.SparepartData = <?= json_encode($spJson, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) ?>;
window.ItemLama = <?= json_encode(array_map(static fn ($i) => [
    'sparepart_id' => (int) $i->sparepart_id,
    'qty'          => (int) $i->qty,
], $items), JSON_HEX_TAG) ?>;
</script>
<script src="<?= base_url('assets/js/service-form.js') ?>"></script>
