<?php

namespace App\Controllers;

use App\Models\BengkelModel;
use App\Models\KendaraanModel;
use App\Models\NotifikasiModel;
use App\Models\ServiceItemModel;
use App\Models\ServiceModel;
use App\Models\SparepartModel;

/**
 * Input service kendaraan & monitoring riwayat service.
 */
class Service extends BaseController
{
    private ServiceModel $serviceModel;
    private ServiceItemModel $itemModel;
    private KendaraanModel $kendaraanModel;

    public function __construct()
    {
        $this->serviceModel  = new ServiceModel();
        $this->itemModel     = new ServiceItemModel();
        $this->kendaraanModel = new KendaraanModel();
    }

    /**
     * Halaman monitoring daftar service yang sudah dilakukan.
     */
    public function index()
    {
        $userId = user_id();

        $filter = [
            'q'        => trim((string) $this->request->getGet('q')),
            'kendaraan'=> (int) $this->request->getGet('kendaraan'),
            'status'   => (string) $this->request->getGet('status'),
            'bulan'    => (string) $this->request->getGet('bulan'),
        ];

        $rows = $this->serviceModel->monitoring($userId, $filter);

        $totalBiaya = 0.0;
        foreach ($rows as $r) {
            $totalBiaya += (float) $r->total_biaya;
        }

        return $this->renderPage('service/index', [
            'title'      => 'Monitoring Service',
            'rows'       => $rows,
            'filter'     => $filter,
            'kendaraan'  => $this->kendaraanModel->milikUser($userId),
            'totalBiaya' => $totalBiaya,
        ]);
    }

    /**
     * Form input service kendaraan.
     */
    public function create()
    {
        $userId    = user_id();
        $kendaraan = $this->kendaraanModel->milikUser($userId);

        if ($kendaraan === []) {
            session()->setFlashdata('error', 'Tambahkan data kendaraan terlebih dahulu.');

            return redirect()->to(site_url('kendaraan/create'));
        }

        return $this->renderPage('service/form', [
            'title'    => 'Input Service',
            'row'      => null,
            'items'    => [],
            'kendaraan'=> $kendaraan,
            'bengkel'  => (new BengkelModel())->orderBy('nama', 'ASC')->findAll(),
            'sparepart'=> (new SparepartModel())->orderBy('kode', 'ASC')->findAll(),
        ]);
    }

    /**
     * Simpan data service + rincian spare part.
     */
    public function save()
    {
        $userId = user_id();

        $kendaraanId = (int) $this->request->getPost('kendaraan_id');
        $kendaraan   = $this->kendaraanModel->where('id', $kendaraanId)->where('user_id', $userId)->first();

        if ($kendaraan === null) {
            return redirect()->back()->withInput()->with('error', 'Kendaraan tidak ditemukan.');
        }

        if (! $this->validate([
            'kendaraan_id'        => 'required|integer',
            'tanggal'             => 'required|valid_date',
            'bengkel_id'          => 'permit_empty|integer',
            'odometer'            => 'permit_empty|integer|greater_than_equal_to[0]',
            'keluhan'             => 'permit_empty|max_length[1000]',
            'teknisi'             => 'permit_empty|max_length[100]',
            'biaya_jasa'          => 'permit_empty|numeric|greater_than_equal_to[0]',
            'status'              => 'required|in_list[dijadwalkan,diproses,selesai]',
            'catatan'             => 'permit_empty|max_length[1000]',
            'next_service_date'   => 'permit_empty|valid_date',
            'next_service_odometer' => 'permit_empty|integer|greater_than_equal_to[0]',
        ], [
            'tanggal.valid_date' => 'Format tanggal tidak valid.',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form input service.');
        }

        // Rincian spare part
        $spIds = (array) $this->request->getPost('sp_id');
        $qtys  = (array) $this->request->getPost('sp_qty');

        $items = [];
        $totalParts = 0.0;

        foreach ($spIds as $i => $spId) {
            $spId = (int) $spId;
            $qty  = isset($qtys[$i]) ? (int) $qtys[$i] : 0;

            if ($spId <= 0 || $qty <= 0) {
                continue;
            }

            $sp = (new SparepartModel())->find($spId);
            if ($sp === null) {
                continue;
            }

            $subtotal = (float) $sp->harga * $qty;
            $items[]  = [
                'sparepart_id' => $spId,
                'nama'         => $sp->nama,
                'qty'          => $qty,
                'harga_satuan' => (float) $sp->harga,
                'subtotal'     => $subtotal,
            ];
            $totalParts += $subtotal;
        }

        $biayaJasa = (float) ($this->request->getPost('biaya_jasa') ?: 0);
        $total     = $totalParts + $biayaJasa;

        $data = [
            'user_id'       => $userId,
            'kendaraan_id'  => $kendaraanId,
            'bengkel_id'    => $this->request->getPost('bengkel_id') ? (int) $this->request->getPost('bengkel_id') : null,
            'tanggal'       => $this->request->getPost('tanggal'),
            'odometer'      => $this->request->getPost('odometer') !== '' ? (int) $this->request->getPost('odometer') : null,
            'keluhan'       => trim((string) $this->request->getPost('keluhan')) ?: null,
            'teknisi'       => trim((string) $this->request->getPost('teknisi')) ?: null,
            'biaya_jasa'    => $biayaJasa,
            'total_biaya'   => $total,
            'status'        => $this->request->getPost('status'),
            'catatan'       => trim((string) $this->request->getPost('catatan')) ?: null,
            'next_service_date'     => $this->request->getPost('next_service_date') ?: null,
            'next_service_odometer' => $this->request->getPost('next_service_odometer') !== ''
                ? (int) $this->request->getPost('next_service_odometer') : null,
        ];

        $db = db_connect();
        $db->transBegin();

        try {
            $serviceId = $this->serviceModel->insert($data, true);

            foreach ($items as $item) {
                $item['service_id'] = $serviceId;
                $this->itemModel->insert($item);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $db->transRollback();

                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data service.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();

            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data service: ' . $e->getMessage());
        }

        session()->setFlashdata('success', 'Data service berhasil disimpan.');

        return redirect()->to(site_url('service/detail/' . $serviceId));
    }

    /**
     * Detail satu data service.
     */
    public function detail($id)
    {
        $row = $this->serviceModel
            ->select('service.*, k.merk, k.tipe, k.no_pol, k.jenis, b.nama AS nama_bengkel, b.alamat AS alamat_bengkel, b.telepon AS telp_bengkel')
            ->join('kendaraan k', 'k.id = service.kendaraan_id')
            ->join('bengkel b', 'b.id = service.bengkel_id', 'left')
            ->where('service.id', (int) $id)
            ->where('service.user_id', user_id())
            ->first();

        if ($row === null) {
            return redirect()->to(site_url('service'))->with('error', 'Data service tidak ditemukan.');
        }

        $pengingat = (new NotifikasiModel())->forKendaraan(user_id(), (int) $row->kendaraan_id);

        return $this->renderPage('service/detail', [
            'title'     => 'Detail Service',
            'row'       => $row,
            'items'     => $this->itemModel->forService((int) $id),
            'pengingat' => $pengingat,
        ]);
    }

    /**
     * Ubah status service.
     */
    public function status($id)
    {
        $row = $this->milik((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('service'))->with('error', 'Data service tidak ditemukan.');
        }

        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['dijadwalkan', 'diproses', 'selesai'], true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->serviceModel->update((int) $id, ['status' => $status]);

        return redirect()->back()->with('success', 'Status service diperbarui menjadi "' . $status . '".');
    }

    /**
     * Hapus data service.
     */
    public function delete($id)
    {
        $row = $this->milik((int) $id);

        if ($row === null) {
            return redirect()->to(site_url('service'))->with('error', 'Data service tidak ditemukan.');
        }

        $this->serviceModel->delete((int) $id);

        return redirect()->to(site_url('service'))->with('success', 'Data service dihapus.');
    }

    /**
     * API JSON detail kendaraan (untuk form input service).
     */
    public function apiKendaraan($id)
    {
        $row = $this->kendaraanModel->where('id', (int) $id)->where('user_id', user_id())->first();

        if ($row === null) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Kendaraan tidak ditemukan']);
        }

        return $this->response->setJSON($row);
    }

    private function milik(int $id): ?object
    {
        return $this->serviceModel->where('id', $id)->where('user_id', user_id())->first();
    }
}
