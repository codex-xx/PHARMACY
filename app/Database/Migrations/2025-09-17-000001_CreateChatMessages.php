<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChatMessages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sender_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'sender_role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => '',
            ],
            'receiver_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'receiver_role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => '',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['sender_id']);
        $this->forge->addKey(['receiver_id']);
        $this->forge->createTable('chat_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('chat_messages', true);
    }
}

?>

