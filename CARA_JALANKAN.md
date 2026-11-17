# Aplikasi Monitoring Service Kendaraan

Aplikasi web mobile-responsive untuk memantau service kendaraan (roda 2 & roda 4),
dibangun dengan **CodeIgniter 4.7.6** + **PHP 8.3** + **MySQL**.

URL akses:

- **http://service.test/** (virtual host — direkomendasikan)
- **http://localhost/service/** (via path)

---

## 1. Akun Demo

| Username | Password  | Role  |
|----------|-----------|-------|
| `admin`  | `admin123`| admin |
| `bengkel`| `bengkel123` | user |

Halaman **Daftar** (`/service/public/register`) juga tersedia untuk membuat akun baru.

---

## 2. Persyaratan

- PHP 8.3 (ekstensi `mysqli`, `intl`, `mbstring`)
- MySQL 5.7/8.x (Laragon: user `root`, password kosong)
- Composer
- Web server:
  - **nginx** (Laragon) — sudah dikonfigurasi otomatis, lihat bagian 5
  - **Apache** — file `.htaccess` sudah disiapkan di root & `public/`

---

## 3. Instalasi

```bash
cd D:/laragon/www/service

# 1. Install dependency (jika vendor belum ada)
composer install

# 2. Buat database + tabel + data awal
"D:/laragon/bin/mysql/mysql-5.7.24-winx64/bin/mysql.exe" -u root < database/service.sql

# 3. Konfigurasi .env (sudah terisi untuk Laragon)
#    database.default.username = root
#    database.default.password = (kosong)
#    database.default.database = service
#    app.baseURL = 'http://localhost/service/public/'
```

Buka: **http://service.test/** (virtual host, lihat bagian 5)
atau **http://localhost/service/**

> Jika memakai `php spark serve` (port 8080), ubah `app.baseURL` di `.env`
> menjadi `http://localhost:8080/` dan `app.indexPage = 'index.php'`.

---

## 4. Daftar Fitur & Halaman

| Fitur | URL | Keterangan |
|-------|-----|------------|
| Login | `/service/public/login` | autentikasi session |
| Daftar | `/service/public/register` | registrasi akun baru |
| Beranda | `/service/public/dashboard` | statistik + pengingat + riwayat terakhir |
| Kendaraan | `/service/public/kendaraan` | CRUD roda 2 / roda 4 (foto, no pol, tahun) |
| Spare Part | `/service/public/sparepart` | CRUD kode, nama, harga, foto, stok + pencarian |
| Bengkel | `/service/public/bengkel` | CRUD bengkel (alamat, telepon, jam buka, layanan) |
| Input Service | `/service/public/service/create` | pilih kendaraan & bengkel, item spare part dinamis, total otomatis, jadwal service berikutnya |
| Monitoring | `/service/public/service` | daftar riwayat service + filter (kendaraan, status, bulan, pencarian) |
| Detail Service | `/service/public/service/detail/{id}` | rincian biaya, ubah status, hapus |
| Notifikasi | `/service/public/notifikasi` | setting pengingat: H-berapa hari, interval, jam, kanal (app/email/WA), tujuan |
| Menu | `/service/public/menu` | navigasi mobile + logout |

Semua halaman (kecuali login & daftar) dilindungi filter `App\Filters\AuthFilter`.

---

## 5. Konfigurasi Server

### nginx (Laragon — yang dipakai saat ini)

Blok berikut sudah ditambahkan ke `D:/laragon/etc/nginx/sites-enabled/00-default.conf`
lalu di-reload (`nginx -s reload`):

```nginx
location = /service  { return 301 /service/public/; }
location = /service/ { return 301 /service/public/; }

location ^~ /service/app/      { deny all; }
location ^~ /service/system/   { deny all; }
location ^~ /service/vendor/   { deny all; }
location ^~ /service/writable/ { deny all; }
location ^~ /service/tests/    { deny all; }
location ^~ /service/database/ { deny all; }

location ~ ^/service/(\.env|env|spark|composer\.json|composer\.lock|phpunit\.dist\.xml|preload\.php|LICENSE|README\.md)$ {
    deny all;
}

location /service/ {
    autoindex off;
    try_files $uri $uri/ /service/public/index.php?$query_string;
}
```

### Virtual host `http://service.test/` (Laragon — direkomendasikan)

Langkah-langkah lengkapnya:

**1. Hostname** — Laragon sudah menambahkan entri ini di
`C:\Windows\System32\drivers\etc\hosts` (cek dengan `ping service.test`):

```
127.0.0.1      service.test         #laragon magic!
```

Jika belum ada, tambahkan baris tersebut (butuh akses admin), atau buka
Laragon → **Menu > Tools > Hosts**.

**2. Konfigurasi nginx** — buat file
`D:/laragon/etc/nginx/sites-enabled/00-service.test.conf`:

```nginx
server {
    listen 80;
    listen 8443 ssl;
    server_name service.test *.service.test;
    root "D:/laragon/www/service/public/";   # root = folder public/

    index index.html index.htm index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        autoindex off;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass php_upstream;
    }

    ssl_certificate     "D:/laragon/etc/ssl/laragon.crt";
    ssl_certificate_key "D:/laragon/etc/ssl/laragon.key";

    charset utf-8;
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # Larang akses file sensitif
    location ~ ^/(\.env|env|spark|composer\.json|composer\.lock|phpunit\.dist\.xml|preload\.php|LICENSE|README\.md)$ {
        deny all;
    }
}
```

> Catatan:
> - Awalan `00-` agar file ini menang jika Laragon membuat ulang
>   `auto.service.test.conf` (file auto bawaan Laragon di-rename menjadi
>   `auto.service.test.conf.disabled` karena root-nya ke folder proyek,
>   bukan `public/`).
> - Nama file boleh lain, asalkan urutan alfabetisnya **sebelum**
>   `auto.service.test.conf`.

**3. Reload nginx:**

```bash
cd D:/laragon/bin/nginx/nginx-1.16.0
./nginx.exe -t
./nginx.exe -s reload
```

**4. `baseURL` dinamis** — `app/Config/App.php` punya konstruktur yang
otomatis menyesuaikan baseURL dengan host yang dipakai:

- `http://service.test/`  → `baseURL = 'http://service.test/'`
- selain itu              → memakai nilai `.env`
  (`http://localhost/service/public/`)

Jadi tidak perlu mengganti `.env` setiap berganti alamat.

**5. Buka** **http://service.test/** — login: `admin / admin123`.

> Untuk Apache, cukup tambahkan VirtualHost:
> ```apache
> <VirtualHost *:80>
>     ServerName service.test
>     DocumentRoot "D:/laragon/www/service/public"
>     <Directory "D:/laragon/www/service/public">
>         AllowOverride All
>         Require all granted
>     </Directory>
> </VirtualHost>
> ```

### Apache

File `.htaccess` di root proyek (rewrite ke `public/`) dan `public/.htaccess`
(front controller) sudah disiapkan — cukup arahkan DocumentRoot ke folder `public/`
atau biarkan root proyek (AllowOverride All).

---

## 6. Struktur Database (`service`)

| Tabel | Fungsi |
|-------|--------|
| `users` | pengguna (login/daftar), password di-hash `password_hash()` |
| `kendaraan` | kendaraan milik user: jenis roda2/roda4, merk, tipe, no pol, tahun, warna, foto |
| `sparepart` | kode, nama, harga, satuan, stok, foto |
| `bengkel` | nama, alamat, telepon, jam buka, layanan, foto |
| `service` | catatan service: tanggal, odometer, keluhan, teknisi, biaya jasa, total, status, **next_service_date / next_service_odometer** |
| `service_item` | rincian spare part per service (qty × harga) |
| `notifikasi` | setting pengingat per kendaraan: aktif, `sebelum_hari`, `interval_hari`, `jam_pengingat`, `kanal`, `tujuan` |

Skema lengkap + data awal: [`database/service.sql`](database/service.sql).

### Logika pengingat (notifikasi)

Jatuh tempo dihitung dari:
1. `service.next_service_date` pada service terakhir (jika diisi), atau
2. tanggal service terakhir + `interval_hari` dari setting notifikasi.

Badge tampil otomatis:
- **merah** → terlambat / jatuh tempo hari ini
- **kuning** → ≤ 7 hari lagi (atau ≤ `sebelum_hari`)
- **hijau** → masih aman

Pengingat tampil di **Beranda** dan **Monitoring Service**.

---

## 7. Upload Foto

Foto disimpan di `public/uploads/{kendaraan|sparepart|bengkel}/`
(otomatis dibuat, validasi MIME: JPG/PNG/WEBP/GIF, maks 2 MB).
File lama otomatis dihapus saat diganti/dihapus.

---

## 8. Struktur Aplikasi

```
app/
├── Config/        Routes, Filters, Database, Autoload
├── Controllers/   Auth, Dashboard, Kendaraan, Sparepart, Bengkel,
│                  Service, Notifikasi, Menu
├── Filters/       AuthFilter (wajib login)
├── Helpers/       app_helper (rupiah, tanggal_id, badge, upload foto)
├── Models/        User, Kendaraan, Sparepart, Bengkel, Service,
│                  ServiceItem, Notifikasi
└── Views/
    ├── layouts/   main.php (sidebar + bottom nav), auth.php
    ├── auth/      login, register
    ├── dashboard/ kendaraan/ sparepart/ bengkel/
    ├── service/   index (monitoring), form (input), detail
    └── notifikasi/, menu/
public/
├── assets/css/app.css, assets/js/*.js, assets/vendor/ (Bootstrap 5 lokal)
└── uploads/{kendaraan,sparepart,bengkel}/
database/service.sql
```

Tampilan: **mobile-first** (bottom navigation di layar kecil, sidebar di ≥ 768 px),
menggunakan Bootstrap 5.3 lokal sehingga tetap jalan tanpa internet.
