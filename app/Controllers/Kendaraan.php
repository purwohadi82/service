<?php

namespace App\Controllers;

use App\Models\KendaraanModel;

/**
 * Manajemen kendaraan (roda 2 & roda 4).
 */
class Kendaraan extends BaseController
{
    private KendaraanModel $model;

    public function __construct()
    {
        $this->model = new KendaraanModel();
    }

    public function index()
    {
        $userId = user_id();

        $rows = $this->model->milikUser($userId);

        // Jumlah service per kendaraan
        $countRows = db_connect()->table('service')
            ->select('kendaraan_id, COUNT(*) AS jml')
            ->where('user_id', $userId)
            ->groupBy('kendaraan_id')
            ->get()->getResult();
        $count = [];
        foreach ($countRows as $r) {
            $count[(int) $r->kendaraan_id] = (int) $r->jml;
        }

        return $this->renderPage('kendaraan/index', [
            'title'  => 'Kendaraan',
            'rows'   => $rows,
            'counts' => $count,
            'roda2'  => count(array_filter($rows, static fn ($k) => $k->jenis === 'roda2')),
            'roda4'  => count(array_filter($rows, static fn ($k) => $k->jenis === 'roda4')),
        ]);
    }

    public function create()
    {
        return $this->renderPage('kendaraan/form', [
            'title' => 'Tambah Kendaraan',
            'row'   => null,
        ]);
    }

    public function edit($id)
    {
        $row = $this->milik((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('kendaraan'))->with('error', 'Data kendaraan tidak ditemukan.');
        }

        return $this->renderPage('kendaraan/form', [
            'title' => 'Ubah Kendaraan',
            'row'   => $row,
        ]);
    }

    public function save()
    {
        $id   = (int) $this->request->getPost('id');
        $row  = $id > 0 ? $this->milik($id) : null;

        if ($id > 0 && $row === null) {
            return redirect()->to(site_url('kendaraan'))->with('error', 'Data kendaraan tidak ditemukan.');
        }

        if (! $this->validate([
            'jenis' => 'required|in_list[roda2,roda4]',
            'merk'  => 'required|max_length[50]',
            'tipe'  => 'permit_empty|max_length[80]',
            'no_pol'=> 'permit_empty|max_length[20]',
            'tahun' => 'permit_empty|integer|greater_than[1970]|less_than_equal_to[2100]',
            'warna' => 'permit_empty|max_length[30]',
            'foto'  => 'mime_in[foto,image/jpeg,image/png,image/webp,image/gif]|max_size[foto,2048]',
        ], [
            'tahun.greater_than' => 'Tahun tidak valid.',
            'foto.max_size'           => 'Ukuran foto maksimal 2 MB.',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data kendaraan.');
        }

        $fotoBaru = simpan_foto($this->request->getFile('foto'), 'kendaraan', $id);
        if ($fotoBaru['error'] !== null) {
            return redirect()->back()->withInput()->with('error', $fotoBaru['error']);
        }

        $data = [
            'user_id' => user_id(),
            'jenis'   => $this->request->getPost('jenis'),
            'merk'    => trim((string) $this->request->getPost('merk')),
            'tipe'    => trim((string) $this->request->getPost('tipe')) ?: null,
            'no_pol'  => strtoupper(trim((string) $this->request->getPost('no_pol'))) ?: null,
            'tahun'   => $this->request->getPost('tahun') !== '' ? (int) $this->request->getPost('tahun') : null,
            'warna'   => trim((string) $this->request->getPost('warna')) ?: null,
        ];

        if ($fotoBaru['nama'] !== null) {
            $data['foto'] = $fotoBaru['nama'];
            hapus_foto($row->foto ?? null, 'kendaraan');
        }

        if ($id > 0) {
            $this->model->update($id, $data);
            session()->setFlashdata('success', 'Data kendaraan berhasil diperbarui.');
        } else {
            $data['user_id'] = user_id();
            $this->model->insert($data);
            session()->setFlashdata('success', 'Kendaraan berhasil ditambahkan.');
        }

        return redirect()->to(site_url('kendaraan'));
    }

    public function delete($id)
    {
        $row = $this->milik((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('kendaraan'))->with('error', 'Data kendaraan tidak ditemukan.');
        }

        hapus_foto($row->foto, 'kendaraan');
        $this->model->delete((int) $id);

        return redirect()->to(site_url('kendaraan'))->with('success', 'Kendaraan dihapus.');
    }

    private function milik(int $id): ?object
    {
        return $this->model->where('id', $id)->where('user_id', user_id())->first();
    }
}
