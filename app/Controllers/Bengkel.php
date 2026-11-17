<?php

namespace App\Controllers;

use App\Models\BengkelModel;

/**
 * Manajemen bengkel kendaraan.
 */
class Bengkel extends BaseController
{
    private BengkelModel $model;

    public function __construct()
    {
        $this->model = new BengkelModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = $this->model->builder()->orderBy('nama', 'ASC');
        if ($q !== '') {
            $builder->groupStart()->like('nama', $q)->orLike('alamat', $q)->groupEnd();
        }

        $rows = $builder->get()->getResult();

        // Jumlah service per bengkel
        $countRows = db_connect()->table('service')
            ->select('bengkel_id, COUNT(*) AS jml')
            ->where('bengkel_id IS NOT NULL', null, false)
            ->groupBy('bengkel_id')
            ->get()->getResult();
        $counts = [];
        foreach ($countRows as $r) {
            $counts[(int) $r->bengkel_id] = (int) $r->jml;
        }

        return $this->renderPage('bengkel/index', [
            'title'  => 'Bengkel',
            'rows'   => $rows,
            'q'      => $q,
            'counts' => $counts,
        ]);
    }

    public function create()
    {
        return $this->renderPage('bengkel/form', ['title' => 'Tambah Bengkel', 'row' => null]);
    }

    public function edit($id)
    {
        $row = $this->model->find((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('bengkel'))->with('error', 'Bengkel tidak ditemukan.');
        }

        return $this->renderPage('bengkel/form', ['title' => 'Ubah Bengkel', 'row' => $row]);
    }

    public function save()
    {
        $id  = (int) $this->request->getPost('id');
        $row = $id > 0 ? $this->model->find($id) : null;

        if ($id > 0 && $row === null) {
            return redirect()->to(site_url('bengkel'))->with('error', 'Bengkel tidak ditemukan.');
        }

        if (! $this->validate([
            'nama'    => 'required|max_length[120]',
            'alamat'  => 'permit_empty|max_length[255]',
            'telepon' => 'permit_empty|max_length[30]',
            'jam_buka'=> 'permit_empty|max_length[60]',
            'layanan' => 'permit_empty|max_length[255]',
            'foto'    => 'mime_in[foto,image/jpeg,image/png,image/webp,image/gif]|max_size[foto,2048]',
        ], ['foto.max_size' => 'Ukuran foto maksimal 2 MB.'])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data bengkel.');
        }

        $fotoBaru = simpan_foto($this->request->getFile('foto'), 'bengkel', $id);
        if ($fotoBaru['error'] !== null) {
            return redirect()->back()->withInput()->with('error', $fotoBaru['error']);
        }

        $data = [
            'nama'    => trim((string) $this->request->getPost('nama')),
            'alamat'  => trim((string) $this->request->getPost('alamat')) ?: null,
            'telepon' => trim((string) $this->request->getPost('telepon')) ?: null,
            'jam_buka'=> trim((string) $this->request->getPost('jam_buka')) ?: null,
            'layanan' => trim((string) $this->request->getPost('layanan')) ?: null,
        ];

        if ($fotoBaru['nama'] !== null) {
            $data['foto'] = $fotoBaru['nama'];
            hapus_foto($row->foto ?? null, 'bengkel');
        }

        if ($id > 0) {
            $this->model->update($id, $data);
            session()->setFlashdata('success', 'Data bengkel berhasil diperbarui.');
        } else {
            $this->model->insert($data);
            session()->setFlashdata('success', 'Bengkel berhasil ditambahkan.');
        }

        return redirect()->to(site_url('bengkel'));
    }

    public function delete($id)
    {
        $row = $this->model->find((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('bengkel'))->with('error', 'Bengkel tidak ditemukan.');
        }

        hapus_foto($row->foto, 'bengkel');
        $this->model->delete((int) $id);

        return redirect()->to(site_url('bengkel'))->with('success', 'Bengkel dihapus.');
    }
}
