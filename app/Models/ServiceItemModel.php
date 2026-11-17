<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceItemModel extends Model
{
    protected $table            = 'service_item';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['service_id', 'sparepart_id', 'nama', 'qty', 'harga_satuan', 'subtotal'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;
    protected $returnType       = 'object';

    /**
     * Rincian spare part sebuah service.
     *
     * @return array<int, object>
     */
    public function forService(int $serviceId): array
    {
        return $this->where('service_id', $serviceId)->findAll();
    }
}
