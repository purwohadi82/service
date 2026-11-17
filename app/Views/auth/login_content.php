<div class="auth-logo"><i class="bi bi-tools"></i></div>
<h1 class="auth-title fs-5 mb-1">Monitoring Service Kendaraan</h1>
<p class="auth-sub mb-4">Masuk untuk mulai memantau service kendaraan Anda.</p>

<form action="<?= site_url('login') ?>" method="post" autocomplete="off">
    <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="username" name="username"
                   placeholder="Masukkan username" value="<?= esc(old('username')) ?>" required autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Masukkan password" required>
            <button class="input-group-text" type="button" id="togglePass" tabindex="-1">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <button class="btn btn-brand w-100 py-2 mt-2" type="submit">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
    </button>
</form>

<div class="text-center mt-3 fs-sm" style="font-size:.87rem">
    Belum punya akun? <a href="<?= site_url('register') ?>" class="fw-bold text-decoration-none" style="color:#0b4a6f">Daftar sekarang</a>
</div>

<div class="mt-3 p-2 rounded-3" style="background:#f1f5f9;font-size:.76rem;color:#7b8794">
    <i class="bi bi-info-circle me-1"></i> Akun demo: <strong>admin / admin123</strong>
</div>

<script>
    document.getElementById('togglePass').addEventListener('click', function () {
        var inp = document.getElementById('password');
        var ic  = this.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            ic.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            ic.className = 'bi bi-eye';
        }
    });
</script>
