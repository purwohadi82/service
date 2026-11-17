<?php

namespace App\Controllers;

use App\Models\KendaraanModel;
use App\Models\NotifikasiModel;

/**
 * Setting notifikasi untuk service berikutnya.
 */
class Notifikasi extends BaseController
{
    private NotifikasiModel $model;

    public function __construct()
    {
        $this->model = new NotifikasiModel();
    }

    /**
     * Daftar setting notifikasi per kendaraan + status pengingat.
     */
    public function index()
    {
        $userId = user_id();

        $pengingat = $this->model->daftarPengingat($userId);

        // Setting default untuk kendaraan yang belum pernah diatur
        $setting = [];
        foreach ($this->model->milikUser($userId) as $s) {
            $setting[(int) $s->kendaraan_id] = $s;
        }

        $kendaraan = (new KendaraanModel())->milikUser($userId);

        if ($kendaraan === []) {
            session()->setFlashdata('error', 'Tambahkan data kendaraan terlebih dahulu.');

            return redirect()->to(site_url('kendaraan/create'));
        }

        return $this->renderPage('notifikasi/index', [
            'title'     => 'Setting Notifikasi',
            'pengingat' => $pengingat,
            'setting'   => $setting,
            'kendaraan' => $kendaraan,
        ]);
    }

    /**
     * Simpan setting notifikasi untuk satu kendaraan (insert / update).
     */
    public function save()
    {
        $userId       = user_id();
        $kendaraanId  = (int) $this->request->getPost('kendaraan_id');

        $kendaraan = (new KendaraanModel())
            ->where('id', $kendaraanId)
            ->where('user_id', $userId)
            ->first();

        if ($kendaraan === null) {
            return redirect()->back()->with('error', 'Kendaraan tidak ditemukan.');
        }

        $jam = (string) $this->request->getPost('jam_pengingat');
        if (strlen($jam) === 5) {
            $jam .= ':00';
        }

        if (! $this->validate([
            'sebelum_hari'  => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[365]',
            'interval_hari' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[1095]',
            'jam_pengingat' => 'required',
            'kanal'         => 'required|in_list[app,email,wa]',
            'tujuan'        => 'permit_empty|max_length[100]',
        ], [
            'sebelum_hari.greater_than_equal_to' => 'Hari pengingat minimal 0.',
            'interval_hari.greater_than_equal_to' => 'Interval service minimal 1 hari.',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali setting notifikasi.');
        }

        $existing = $this->model->forKendaraan($userId, $kendaraanId);

        // Checkbox "aktif" dikirim dari form toggle; jika tidak, pertahankan nilai lama
        $aktifPost = $this->request->getPost('aktif');
        $aktif = $aktifPost !== null
            ? ($aktifPost ? 1 : 0)
            : ($existing !== null ? (int) $existing->aktif : 1);

        $data = [
            'user_id'       => $userId,
            'kendaraan_id'  => $kendaraanId,
            'aktif'         => $aktif,
            'sebelum_hari'  => (int) $this->request->getPost('sebelum_hari'),
            'interval_hari' => (int) $this->request->getPost('interval_hari'),
            'jam_pengingat' => $jam,
            'kanal'         => (string) $this->request->getPost('kanal'),
            'tujuan'        => trim((string) $this->request->getPost('tujuan')) ?: null,
        ];

        if ($existing !== null) {
            $this->model->update((int) $existing->id, $data);
            session()->setFlashdata('success', 'Setting notifikasi "' . $kendaraan->merk . '" diperbarui.');
        } else {
            $this->model->insert($data);
            session()->setFlashdata('success', 'Notifikasi untuk "' . $kendaraan->merk . '" diaktifkan.');
        }

        return redirect()->to(site_url('notifikasi'));
    }

    /**
     * Nyalakan / matikan notifikasi cepat (switch di dashboard & monitoring).
     */
    public function toggle()
    {
        $userId      = user_id();
        $kendaraanId = (int) $this->request->getPost('kendaraan_id');
        $aktif       = $this->request->getPost('aktif') ? 1 : 0;

        $kendaraan = (new KendaraanModel())
            ->where('id', $kendaraanId)
            ->where('user_id', $userId)
            ->first();

        if ($kendaraan === null) {
            return redirect()->back()->with('error', 'Kendaraan tidak ditemukan.');
        }

        $existing = $this->model->forKendaraan($userId, $kendaraanId);

        if ($existing !== null) {
            $this->model->update((int) $existing->id, ['aktif' => $aktif]);
        } else {
            $this->model->insert([
                'user_id'      => $userId,
                'kendaraan_id' => $kendaraanId,
                'aktif'        => $aktif,
            ]);
        }

        return redirect()->back()->with('success', $aktif ? 'Notifikasi diaktifkan.' : 'Notifikasi dimatikan.');
    }
}
