<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactAndResetToUsers extends Migration
{
    public function up()
    {
        // Add columns if they do not already exist
        $fields = [];

        if (!$this->db->getFieldData('users') || !array_column($this->db->getFieldData('users'), 'name')) {
            // Safeguard; if table meta can't be read, just attempt to add all columns
            $fields = [
                'email' => [
                    'type' => 'VARCHAR',
                    'constraint' => '191',
                    'null' => true,
                ],
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
                'password_reset_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                ],
                'password_reset_expires_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];
        } else {
            $existing = array_map(static function($f) { return $f->name; }, $this->db->getFieldData('users'));
            if (!in_array('email', $existing, true)) {
                $fields['email'] = [
                    'type' => 'VARCHAR',
                    'constraint' => '191',
                    'null' => true,
                ];
            }
            if (!in_array('phone', $existing, true)) {
                $fields['phone'] = [
                    'type' => 'VARCHAR',
                    'constraint' => '32',
                    'null' => true,
                ];
            }
            if (!in_array('phone_verification_code', $existing, true)) {
                $fields['phone_verification_code'] = [
                    'type' => 'VARCHAR',
                    'constraint' => '10',
                    'null' => true,
                ];
            }
            if (!in_array('phone_verified_at', $existing, true)) {
                $fields['phone_verified_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }
            if (!in_array('password_reset_token', $existing, true)) {
                $fields['password_reset_token'] = [
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => true,
                ];
            }
            if (!in_array('password_reset_expires_at', $existing, true)) {
                $fields['password_reset_expires_at'] = [
                    'type' => 'DATETIME',
                    'null' => true,
                ];
            }
        }

        if (!empty($fields)) {
            $this->forge->addColumn('users', $fields);
        }

        // Unique keys for email/phone when present
        try {
            $this->db->query('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        } catch (\Throwable $e) {
            // ignore if exists
        }
        try {
            $this->db->query('CREATE UNIQUE INDEX users_phone_unique ON users (phone)');
        } catch (\Throwable $e) {
            // ignore if exists
        }
    }

    public function down()
    {
        $columns = [
            'email',
            'phone',
            'phone_verification_code',
            'phone_verified_at',
            'password_reset_token',
            'password_reset_expires_at',
        ];
        foreach ($columns as $col) {
            try {
                $this->forge->dropColumn('users', $col);
            } catch (\Throwable $e) {
                // ignore
            }
        }
        try { $this->db->query('DROP INDEX users_email_unique ON users'); } catch (\Throwable $e) {}
        try { $this->db->query('DROP INDEX users_phone_unique ON users'); } catch (\Throwable $e) {}
    }
}


