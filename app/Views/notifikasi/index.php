<?php
/**
 * Setting notifikasi service berikutnya.
 * $pengingat = hasil NotifikasiModel::daftarPengingat()
 */
$aktifCount = count(array_filter($pengingat, static fn ($p) => $p->aktif));
?>

<h1 class="page-title">Setting Notifikasi</h1>
<p class="page-subtitle">Atur pengingat untuk service kendaraan berikutnya.</p>

<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#0b4a6f,#1a8fc4)"><i class="bi bi-bell"></i></span>
            <div>
                <div class="stat-num"><?= $aktifCount ?> / <?= count($pengingat) ?></div>
                <div class="stat-label">Notifikasi Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card">
            <span class="stat-icon" style="background:linear-gradient(135deg,#ff8a00,#ff5e00)"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="stat-num"><?= count(array_filter($pengingat, static fn ($p) => $p->aktif && $p->jatuh_tempo && $p->sisa_hari <= $p->sebelum_hari)) ?></div>
                <div class="stat-label">Perlu Perhatian</div>
            </div>
        </div>
    </div>
</div>

<?php foreach ($pengingat as $p):
    $k    = $p->kendaraan;
    $badge = status_jatuh_tempo($p->jatuh_tempo);
    $val  = $setting[(int) $k->id] ?? null;
    $kanal = old('kanal', $p->kanal ?? 'app');
    $tujuanPlaceholder = $kanal === 'email' ? 'contoh@email.com' : ($kanal === 'wa' ? '08xxxxxxxxxx' : 'Notifikasi di dalam aplikasi');
?>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="bi <?= $k->jenis === 'roda4' ? 'bi-car-front' : 'bi-bicycle' ?> me-1"></i>
                <?= esc($k->merk . ' ' . ($k->tipe ?? '')) ?>
            </span>
            <span class="small text-muted"><?= $k->no_pol ? esc($k->no_pol) : ($k->jenis === 'roda4' ? 'Roda 4' : 'Roda 2') ?></span>
        </div>

        <div class="list-row" style="padding:.7rem 1rem">
            <div class="row-main">
                <div class="row-title" style="font-size:.87rem">
                    Service terakhir: <?= $p->service_terakhir ? tanggal_id($p->service_terakhir->tanggal) : 'belum ada' ?>
                </div>
                <div class="row-sub">
                    Jatuh tempo: <strong><?= $p->jatuh_tempo ? tanggal_id($p->jatuh_tempo) : 'belum diatur' ?></strong>
                    <?php if ($p->sumber): ?>&middot; <span class="text-muted">(<?= esc($p->sumber) ?>)</span><?php endif; ?>
                </div>
            </div>
            <div class="row-end">
                <?php if ($badge !== null): ?>
                    <span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cepat: nyalakan / matikan -->
        <form action="<?= site_url('notifikasi/toggle') ?>" method="post" class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <input type="hidden" name="kendaraan_id" value="<?= $k->id ?>">
            <span class="small fw-semibold" style="color:#41505f">Notifikasi <?= $p->aktif ? 'aktif' : 'nonaktif' ?></span>
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="sw<?= $k->id ?>"
                       <?= $p->aktif ? 'checked' : '' ?> onchange="this.form.submit()" name="aktif" value="1">
                <label class="form-check-label small" for="sw<?= $k->id ?>"></label>
            </div>
        </form>

        <form action="<?= site_url('notifikasi/save') ?>" method="post" class="card-body">
            <input type="hidden" name="kendaraan_id" value="<?= $k->id ?>">

            <div class="row g-2">
                <div class="col-4 mb-2">
                    <label class="form-label">Ingatkan <span class="text-muted">(hari)</span></label>
                    <input type="number" class="form-control" name="sebelum_hari" min="0" max="365"
                           value="<?= esc(old('sebelum_hari', $p->sebelum_hari)) ?>" required>
                    <div class="form-text">Sebelum jatuh tempo</div>
                </div>

                <div class="col-4 mb-2">
                    <label class="form-label">Interval <span class="text-muted">(hari)</span></label>
                    <input type="number" class="form-control" name="interval_hari" min="1" max="1095"
                           value="<?= esc(old('interval_hari', $val->interval_hari ?? 180)) ?>" required>
                    <div class="form-text">Jika tanpa jadwal</div>
                </div>

                <div class="col-4 mb-2">
                    <label class="form-label">Jam</label>
                    <input type="time" class="form-control" name="jam_pengingat"
                           value="<?= esc(old('jam_pengingat', $p->jam)) ?>" required>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-6 mb-2">
                    <label class="form-label">Kanal</label>
                    <select class="form-select" name="kanal" id="kanal_<?= $k->id ?>">
                        <option value="app" <?= $kanal === 'app' ? 'selected' : '' ?>>Aplikasi</option>
                        <option value="email" <?= $kanal === 'email' ? 'selected' : '' ?>>Email</option>
                        <option value="wa" <?= $kanal === 'wa' ? 'selected' : '' ?>>WhatsApp</option>
                    </select>
                </div>

                <div class="col-6 mb-2">
                    <label class="form-label">Tujuan (<?= $kanal === 'app' ? 'opsional' : 'wajib utk email/wa' ?>)</label>
                    <input type="text" class="form-control" name="tujuan" maxlength="100"
                           placeholder="<?= esc($tujuanPlaceholder) ?>"
                           value="<?= esc(old('tujuan', $p->tujuan)) ?>">
                </div>
            </div>

            <div class="small text-muted mb-3">
                <i class="bi bi-info-circle"></i>
                <?php if ($p->sumber): ?>
                    Pengingat berdasarkan <?= esc($p->sumber) ?>.
                <?php else: ?>
                    Belum ada riwayat service; interval di atas dipakai untuk menghitung jatuh tempo.
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2">
                <i class="bi bi-bell me-1"></i> Simpan Setting Notifikasi
            </button>
        </form>
    </div>
<?php endforeach; ?>

<div class="alert alert-light border small">
    <i class="bi bi-lightbulb me-1 text-warning"></i>
    Pengingat ditampilkan otomatis di <a href="<?= site_url('dashboard') ?>" class="fw-bold text-decoration-none">Beranda</a>
    dan <a href="<?= site_url('service') ?>" class="fw-bold text-decoration-none">Monitoring Service</a> saat mendekati jadwal service.
</div>
