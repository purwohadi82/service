<?php
$statusLabel = ['' => 'Semua Status', 'selesai' => 'Selesai', 'diproses' => 'Diproses', 'dijadwalkan' => 'Dijadwalkan'];
?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <h1 class="page-title">Monitoring Service</h1>
        <p class="page-subtitle">Daftar service kendaraan yang sudah dilakukan.</p>
    </div>
    <a href="<?= site_url('service/create') ?>" class="btn btn-brand btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Input
    </a>
</div>

<div class="hero mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h5>Total biaya (hasil filter)</h5>
            <div class="hero-value"><?= rupiah($totalBiaya) ?></div>
            <div class="hero-meta"><?= count($rows) ?> catatan service</div>
        </div>
        <div class="col-auto position-relative" style="z-index:1">
            <i class="bi bi-clipboard-check" style="font-size:3rem;opacity:.35"></i>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form action="<?= site_url('service') ?>" method="get" class="row g-2">
            <div class="col-12">
                <input type="search" class="form-control" name="q" placeholder="Cari merk, tipe, no pol, keluhan..."
                       value="<?= esc($filter['q']) ?>">
            </div>

            <div class="col-6">
                <select class="form-select form-select-sm" name="kendaraan">
                    <option value="">Semua Kendaraan</option>
                    <?php foreach ($kendaraan as $k): ?>
                        <option value="<?= $k->id ?>" <?= (int) $filter['kendaraan'] === (int) $k->id ? 'selected' : '' ?>>
                            <?= esc($k->merk . ' ' . ($k->tipe ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-6">
                <select class="form-select form-select-sm" name="status">
                    <?php foreach ($statusLabel as $val => $lab): ?>
                        <option value="<?= $val ?>" <?= $filter['status'] === $val ? 'selected' : '' ?>><?= $lab ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-7">
                <input type="month" class="form-control form-control-sm" name="bulan" value="<?= esc($filter['bulan']) ?>">
            </div>
            <div class="col-5 d-flex gap-1">
                <button class="btn btn-brand btn-sm flex-fill" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="<?= site_url('service') ?>" class="btn btn-outline-secondary btn-sm" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<?php if ($rows === []): ?>
    <div class="empty-state">
        <i class="bi bi-clipboard-x"></i>
        Belum ada data service yang cocok dengan filter.<br>
        <a href="<?= site_url('service/create') ?>" class="btn btn-brand btn-sm mt-3">
            <i class="bi bi-plus-lg me-1"></i> Input Service
        </a>
    </div>
<?php else: ?>
    <div class="card mb-3">
        <?php foreach ($rows as $s):
            $jatuh = $s->status === 'selesai' && $s->next_service_date ? status_jatuh_tempo($s->next_service_date) : null;
        ?>
            <a class="list-row" href="<?= site_url('service/detail/' . $s->id) ?>">
                <span class="thumb">
                    <i class="bi <?= $s->jenis === 'roda4' ? 'bi-car-front' : 'bi-bicycle' ?>"></i>
                </span>

                <div class="row-main">
                    <div class="row-title"><?= esc($s->merk . ' ' . ($s->tipe ?? '')) ?></div>
                    <div class="row-sub">
                        <?= tanggal_id($s->tanggal) ?>
                        <?php if ($s->no_pol): ?> &middot; <span class="fw-semibold"><?= esc($s->no_pol) ?></span><?php endif; ?>
                    </div>
                    <div class="row-sub"><i class="bi bi-building"></i> <?= esc($s->nama_bengkel ?? '-') ?></div>
                    <?php if ($jatuh !== null): ?>
                        <div class="mt-1"><span class="badge <?= $jatuh['class'] ?>"><i class="bi bi-bell"></i> Service berikutnya: <?= tanggal_id($s->next_service_date) ?> (<?= $jatuh['label'] ?>)</span></div>
                    <?php endif; ?>
                </div>

                <div class="row-end">
                    <div class="fw-bold" style="color:#0b4a6f;font-size:.92rem"><?= rupiah($s->total_biaya) ?></div>
                    <div class="mt-1"><?= badge_status($s->status) ?></div>
                    <div class="text-muted" style="font-size:.72rem"><?= rupiah($s->biaya_jasa) ?> jasa</div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <p class="text-center text-muted" style="font-size:.78rem">
        Menampilkan <?= count($rows) ?> catatan service
    </p>
<?php endif; ?>
