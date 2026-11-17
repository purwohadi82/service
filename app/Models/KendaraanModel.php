<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table            = 'kendaraan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'jenis', 'merk', 'tipe', 'no_pol', 'tahun', 'warna', 'foto', 'created_at', 'updated_at'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'object';

    protected $validationRules = [
        'jenis' => 'required|in_list[roda2,roda4]',
        'merk'  => 'required|max_length[50]',
    ];

    /**
     * Daftar kendaraan milik user tertentu (untuk dropdown).
     *
     * @return array<int, object>
     */
    public function milikUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('merk', 'ASC')->findAll();
    }

    public function label(object $k): string
    {
        $roda = $k->jenis === 'roda4' ? 'Roda 4' : 'Roda 2';

        return trim($k->merk . ' ' . ($k->tipe ?? '') . ' (' . $roda . ')' . ($k->no_pol ? ' - ' . $k->no_pol : ''));
    }
}
