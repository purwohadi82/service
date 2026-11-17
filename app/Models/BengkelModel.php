<?php

namespace App\Models;

use CodeIgniter\Model;

class BengkelModel extends Model
{
    protected $table            = 'bengkel';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama', 'alamat', 'telepon', 'jam_buka', 'layanan', 'foto', 'created_at', 'updated_at'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = true;
    protected $returnType       = 'object';

    protected $validationRules = [
        'nama' => 'required|max_length[120]',
    ];
}
