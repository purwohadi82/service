<?php

namespace App\Controllers;

use App\Models\SparepartModel;

/**
 * Manajemen spare part (kode, nama, harga, foto).
 */
class Sparepart extends BaseController
{
    private SparepartModel $model;

    public function __construct()
    {
        $this->model = new SparepartModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = $this->model->builder();
        $builder->orderBy('kode', 'ASC');

        if ($q !== '') {
            $builder->groupStart()->like('kode', $q)->orLike('nama', $q)->groupEnd();
        }

        $rows = $builder->get()->getResult();

        return $this->renderPage('sparepart/index', [
            'title' => 'Spare Part',
            'rows'  => $rows,
            'q'     => $q,
            'total' => count($rows),
        ]);
    }

    public function create()
    {
        // Kode otomatis berikutnya
        $last = $this->model->selectMax('kode')->get()->getRow();
        $next = 'SP-' . str_pad((string) (((int) preg_replace('/\D/', '', (string) ($last->kode ?? ''))) + 1), 4, '0', STR_PAD_LEFT);

        return $this->renderPage('sparepart/form', [
            'title' => 'Tambah Spare Part',
            'row'   => null,
            'nextCode' => $next,
        ]);
    }

    public function edit($id)
    {
        $row = $this->model->find((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('sparepart'))->with('error', 'Spare part tidak ditemukan.');
        }

        return $this->renderPage('sparepart/form', [
            'title'     => 'Ubah Spare Part',
            'row'       => $row,
            'nextCode'  => $row->kode,
        ]);
    }

    public function save()
    {
        $id  = (int) $this->request->getPost('id');
        $row = $id > 0 ? $this->model->find($id) : null;

        if ($id > 0 && $row === null) {
            return redirect()->to(site_url('sparepart'))->with('error', 'Spare part tidak ditemukan.');
        }

        $rules = [
            'kode'  => 'required|max_length[30]',
            'nama'  => 'required|max_length[120]',
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok'  => 'permit_empty|integer|greater_than_equal_to[0]',
            'foto'  => 'mime_in[foto,image/jpeg,image/png,image/webp,image/gif]|max_size[foto,2048]',
        ];

        // Cek keunikan kode (kecuali saat edit sendiri)
        $kode = trim((string) $this->request->getPost('kode'));
        $dup  = $this->model->where('kode', $kode)->where('id !=', $id)->first();
        if ($dup !== null) {
            return redirect()->back()->withInput()->with('error', 'Kode spare part sudah dipakai.');
        }

        if (! $this->validate($rules, ['foto.max_size' => 'Ukuran foto maksimal 2 MB.'])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data spare part.');
        }

        $fotoBaru = simpan_foto($this->request->getFile('foto'), 'sparepart', $id);
        if ($fotoBaru['error'] !== null) {
            return redirect()->back()->withInput()->with('error', $fotoBaru['error']);
        }

        $data = [
            'kode'  => $kode,
            'nama'  => trim((string) $this->request->getPost('nama')),
            'harga' => (float) str_replace([',', ' '], '', (string) $this->request->getPost('harga')),
            'satuan'=> trim((string) $this->request->getPost('satuan')) ?: 'pcs',
            'stok'  => $this->request->getPost('stok') !== '' ? (int) $this->request->getPost('stok') : 0,
        ];

        if ($fotoBaru['nama'] !== null) {
            $data['foto'] = $fotoBaru['nama'];
            hapus_foto($row->foto ?? null, 'sparepart');
        }

        if ($id > 0) {
            $this->model->update($id, $data);
            session()->setFlashdata('success', 'Spare part berhasil diperbarui.');
        } else {
            $this->model->insert($data);
            session()->setFlashdata('success', 'Spare part berhasil ditambahkan.');
        }

        return redirect()->to(site_url('sparepart'));
    }

    public function delete($id)
    {
        $row = $this->model->find((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('sparepart'))->with('error', 'Spare part tidak ditemukan.');
        }

        hapus_foto($row->foto, 'sparepart');
        $this->model->delete((int) $id);

        return redirect()->to(site_url('sparepart'))->with('success', 'Spare part dihapus.');
    }
}
