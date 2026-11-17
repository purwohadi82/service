<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username', 'password', 'nama', 'email', 'no_hp', 'role', 'created_at', 'updated_at'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;
    protected $returnType       = 'object';

    protected $validationRules = [
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username]',
        'nama'     => 'required|max_length[100]',
        'password' => 'required|min_length[6]',
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'Username sudah digunakan, silakan pilih yang lain.',
        ],
    ];

    public function baru(array $data): int
    {
        $data['password']    = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['role']        = $data['role'] ?? 'user';
        $data['created_at']  = date('Y-m-d H:i:s');

        $this->insert($data);

        return (int) $this->getInsertID();
    }
}
