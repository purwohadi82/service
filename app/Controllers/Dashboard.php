<?php

namespace App\Controllers;

use App\Models\KendaraanModel;
use App\Models\NotifikasiModel;
use App\Models\ServiceModel;

/**
 * Dashboard / beranda aplikasi.
 */
class Dashboard extends BaseController
{
    public function index()
    {
        $userId = user_id();

        $kendaraanModel = new KendaraanModel();
        $serviceModel   = new ServiceModel();
        $notifModel     = new NotifikasiModel();

        $kendaraan = $kendaraanModel->milikUser($userId);

        $serviceBulanIni = $serviceModel->where('user_id', $userId)
            ->where('tanggal >=', date('Y-m-01'))
            ->where('tanggal <=', date('Y-m-t'))
            ->countAllResults();

        // Pengingat service berikutnya (aktif & mendekat / terlambat)
        $pengingat = array_values(array_filter($notifModel->daftarPengingat($userId), static function ($p) {
            if (! $p->aktif || $p->jatuh_tempo === null) {
                return false;
            }

            return $p->sisa_hari <= $p->sebelum_hari;
        }));

        usort($pengingat, static fn ($a, $b) => $a->sisa_hari <=> $b->sisa_hari);

        $data = [
            'title'         => 'Beranda',
            'kendaraan'     => $kendaraan,
            'jumlahKendaraan' => count($kendaraan),
            'jumlahService' => $serviceModel->where('user_id', $userId)->countAllResults(),
            'serviceBulanIni' => $serviceBulanIni,
            'totalBiaya'    => $serviceModel->totalBiaya($userId),
            'terakhir'      => $serviceModel->monitoring($userId, []),
            'pengingat'     => array_slice($pengingat, 0, 5),
            'jumlahPengingat' => count($pengingat),
        ];

        return $this->renderPage('dashboard/index', $data);
    }
}
