<?php

namespace App\Models;

use CodeIgniter\Model;

class SparepartModel extends Model
{
    protected $table            = 'sparepart';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['kode', 'nama', 'harga', 'satuan', 'stok', 'foto', 'created_at', 'updated_at'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'object';

    protected $validationRules = [
        'kode'  => 'required|max_length[30]|is_unique[sparepart.kode,id,{id}]',
        'nama'  => 'required|max_length[120]',
        'harga' => 'required|numeric|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'kode' => [
            'is_unique' => 'Kode spare part sudah dipakai.',
        ],
    ];
}
