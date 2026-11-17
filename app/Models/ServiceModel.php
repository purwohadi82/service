<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table            = 'service';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id', 'kendaraan_id', 'bengkel_id', 'tanggal', 'odometer', 'keluhan',
        'teknisi', 'total_biaya', 'biaya_jasa', 'status', 'catatan',
        'next_service_date', 'next_service_odometer', 'created_at', 'updated_at',
    ];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'object';

    protected $validationRules = [
        'kendaraan_id' => 'required|integer',
        'tanggal'      => 'required|valid_date',
        'status'       => 'required|in_list[dijadwalkan,diproses,selesai]',
    ];

    /**
     * Monitoring list milik user dengan filter opsional.
     *
     * @param array{q?:string,kendaraan?:int,status?:string,bulan?:string} $filter
     *
     * @return array<int, object>
     */
    public function monitoring(int $userId, array $filter = []): array
    {
        $builder = $this->builder();
        $builder->select('service.*, k.merk, k.tipe, k.no_pol, k.jenis, b.nama AS nama_bengkel')
            ->join('kendaraan k', 'k.id = service.kendaraan_id')
            ->join('bengkel b', 'b.id = service.bengkel_id', 'left')
            ->where('service.user_id', $userId)
            ->orderBy('service.tanggal', 'DESC')
            ->orderBy('service.id', 'DESC');

        if (! empty($filter['q'])) {
            $q = $filter['q'];
            $builder->groupStart()
                ->like('k.merk', $q)
                ->like('k.tipe', $q)
                ->like('k.no_pol', $q)
                ->like('service.keluhan', $q)
                ->like('service.teknisi', $q)
                ->groupEnd();
        }

        if (! empty($filter['kendaraan'])) {
            $builder->where('service.kendaraan_id', (int) $filter['kendaraan']);
        }

        if (! empty($filter['status'])) {
            $builder->where('service.status', $filter['status']);
        }

        if (! empty($filter['bulan'])) {
            $builder->where('service.tanggal >=', $filter['bulan'] . '-01');
            $builder->where('service.tanggal <=', date('Y-m-t', strtotime($filter['bulan'] . '-01')));
        }

        return $builder->get()->getResult();
    }

    /**
     * Total biaya service milik user.
     */
    public function totalBiaya(int $userId): float
    {
        $row = $this->selectSum('total_biaya')->where('user_id', $userId)->get()->getRow();

        return $row !== null && $row->total_biaya !== null ? (float) $row->total_biaya : 0.0;
    }
}
