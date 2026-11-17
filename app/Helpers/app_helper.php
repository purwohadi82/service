<?php

/**
 * Helper khusus aplikasi Monitoring Service Kendaraan
 */

if (! function_exists('rupiah')) {
    /**
     * Format rupiah: 150000 -> Rp 150.000
     */
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal Indonesia: 2026-11-16 -> 16 Nov 2026
     * Tambahkan $jam = true untuk menampilkan jam.
     */
    function tanggal_id(?string $tanggal, bool $jam = false): string
    {
        if ($tanggal === null || $tanggal === '') {
            return '-';
        }

        $ts    = strtotime($tanggal);
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];

        $hasil = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

        if ($jam) {
            $hasil .= ', ' . date('H.i', $ts);
        }

        return $hasil;
    }
}

if (! function_exists('user_id')) {
    /**
     * ID user yang sedang login.
     */
    function user_id(): ?int
    {
        $id = session()->get('user_id');

        return $id !== null ? (int) $id : null;
    }
}

if (! function_exists('badge_status')) {
    /**
     * Badge HTML untuk status service.
     */
    function badge_status(string $status): string
    {
        $map = [
            'selesai'    => ['bg-success', 'Selesai'],
            'diproses'   => ['bg-warning text-dark', 'Diproses'],
            'dijadwalkan'=> ['bg-info text-dark', 'Dijadwalkan'],
        ];

        [$class, $label] = $map[$status] ?? ['bg-secondary', ucfirst($status)];

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }
}

if (! function_exists('status_jatuh_tempo')) {
    /**
     * Status pengingat service berikutnya berdasarkan tanggal jatuh tempo.
     *
     * @return array{label:string,class:string}|null null jika belum ada jadwal
     */
    function status_jatuh_tempo(?string $jatuhTempo): ?array
    {
        if ($jatuhTempo === null || $jatuhTempo === '') {
            return null;
        }

        $today  = new DateTime('today');
        $target = new DateTime($jatuhTempo);
        $diff   = (int) $today->diff($target)->format('%r%a'); // negatif = lewat

        if ($diff < 0) {
            return ['label' => 'Terlambat ' . abs($diff) . ' hari', 'class' => 'bg-danger'];
        }

        if ($diff === 0) {
            return ['label' => 'Jatuh tempo hari ini', 'class' => 'bg-danger'];
        }

        if ($diff <= 7) {
            return ['label' => 'Tinggal ' . $diff . ' hari', 'class' => 'bg-warning text-dark'];
        }

        return ['label' => $diff . ' hari lagi', 'class' => 'bg-success'];
    }
}

if (! function_exists('simpan_foto')) {
    /**
     * Simpan upload foto ke public/uploads/{folder}.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     *
     * @return array{nama: ?string, error: ?string}
     */
    function simpan_foto($file, string $folder, int $id = 0): array
    {
        if (! $file instanceof \CodeIgniter\HTTP\Files\UploadedFile || ! $file->isValid() || $file->hasMoved()) {
            return ['nama' => null, 'error' => null];
        }

        $mime  = (string) $file->getMimeType();
        $boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

        if (! array_key_exists($mime, $boleh)) {
            return ['nama' => null, 'error' => 'Foto harus berformat JPG, PNG, WEBP, atau GIF.'];
        }

        $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $nama = $folder . '_' . ($id > 0 ? $id . '_' : '') . time() . '_' . bin2hex(random_bytes(3)) . '.' . $boleh[$mime];
        $file->move($dir, $nama, true);

        return ['nama' => $nama, 'error' => null];
    }
}

if (! function_exists('hapus_foto')) {
    /**
     * Hapus file foto lama.
     */
    function hapus_foto(?string $namaFile, string $folder): void
    {
        if ($namaFile === null || $namaFile === '') {
            return;
        }

        $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . basename($namaFile);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
