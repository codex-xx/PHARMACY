<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $passwordHash = password_hash('password', PASSWORD_DEFAULT);
        $data = [
            'username' => 'admin',
            'password_hash' => $passwordHash,
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('users');
        $existing = $builder->getWhere(['username' => 'admin'])->getRow();
        if ($existing) {
            $builder->where('id', $existing->id)->update($data);
        } else {
            $builder->insert($data);
        }
    }
}
