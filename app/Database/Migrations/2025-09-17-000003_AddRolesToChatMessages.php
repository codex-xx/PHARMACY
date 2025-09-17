<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRolesToChatMessages extends Migration
{
    public function up()
    {
        $fields = [
            'sender_role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => '',
                'after' => 'sender_id',
            ],
            'receiver_role' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => '',
                'after' => 'receiver_id',
            ],
        ];
        $this->forge->addColumn('chat_messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_messages', 'sender_role');
        $this->forge->dropColumn('chat_messages', 'receiver_role');
    }
}

?>

