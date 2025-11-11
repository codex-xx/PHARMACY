<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username' => 'admin',
                'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'admin',
                'email' => 'carlvesteralbarina@gmail.com',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'cashier',
                'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'cashier',
                'email' => 'vesterlaurel@gmail.com',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('users');
        foreach ($users as $u) {
            $exists = $builder->where('username', $u['username'])->get()->getRowArray();
            if (!$exists) {
                $builder->insert($u);
            }
        }
    }
}

?>

