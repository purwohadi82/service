<div class="auth-logo"><i class="bi bi-person-plus"></i></div>
<h1 class="auth-title fs-5 mb-1">Buat Akun Baru</h1>
<p class="auth-sub mb-4">Daftar gratis untuk memantau service kendaraan Anda.</p>

<form action="<?= site_url('register') ?>" method="post" autocomplete="off">
    <div class="mb-3">
        <label class="form-label req" for="nama">Nama Lengkap</label>
        <input type="text" class="form-control" id="nama" name="nama" maxlength="100"
               placeholder="Nama Anda" value="<?= esc(old('nama')) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label req" for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" maxlength="50"
               placeholder="min. 3 karakter (huruf/angka)" value="<?= esc(old('username')) ?>" required>
    </div>

    <div class="row g-2">
        <div class="col-7 mb-3">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="100"
                   placeholder="opsional" value="<?= esc(old('email')) ?>">
        </div>
        <div class="col-5 mb-3">
            <label class="form-label" for="no_hp">No. HP</label>
            <input type="tel" class="form-control" id="no_hp" name="no_hp" maxlength="20"
                   placeholder="opsional" value="<?= esc(old('no_hp')) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label req" for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password"
               minlength="6" placeholder="min. 6 karakter" required>
    </div>

    <div class="mb-3">
        <label class="form-label req" for="confirm">Ulangi Password</label>
        <input type="password" class="form-control" id="confirm" name="confirm" minlength="6" required>
    </div>

    <button class="btn btn-accent w-100 py-2 mt-1" type="submit">
        <i class="bi bi-check2-circle me-1"></i> Daftar &amp; Masuk
    </button>
</form>

<div class="text-center mt-3" style="font-size:.87rem">
    Sudah punya akun? <a href="<?= site_url('login') ?>" class="fw-bold text-decoration-none" style="color:#0b4a6f">Masuk</a>
</div>
