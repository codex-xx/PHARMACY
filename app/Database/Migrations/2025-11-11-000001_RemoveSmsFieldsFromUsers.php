<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSmsFieldsFromUsers extends Migration
{
    public function up()
    {
        $columns = [
            'phone',
            'phone_verification_code',
            'phone_verified_at',
        ];
        
        foreach ($columns as $col) {
            try {
                $this->forge->dropColumn('users', $col);
            } catch (\Throwable $e) {
                // ignore if column doesn't exist
            }
        }
        
        // Drop unique indexes if they exist
        try {
            $this->db->query('DROP INDEX users_phone_unique ON users');
        } catch (\Throwable $e) {
            // ignore if index doesn't exist
        }
    }

    public function down()
    {
        // Rollback: add columns back if needed
        $fields = [
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ],
            'phone_verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ],
            'phone_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
        
        try {
            $this->db->query('CREATE UNIQUE INDEX users_phone_unique ON users (phone)');
        } catch (\Throwable $e) {
            // ignore if index already exists
        }
    }
}
