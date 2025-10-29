<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRolesToChatMessages extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (!$db->fieldExists('sender_role', 'chat_messages')) {
			$this->forge->addColumn('chat_messages', [
				'sender_role' => [
					'type' => 'VARCHAR',
					'constraint' => 20,
					'null' => false,
					'default' => '',
					'after' => 'sender_id',
				],
			]);
		}

        if (!$db->fieldExists('receiver_role', 'chat_messages')) {
			$this->forge->addColumn('chat_messages', [
				'receiver_role' => [
					'type' => 'VARCHAR',
					'constraint' => 20,
					'null' => false,
					'default' => '',
					'after' => 'receiver_id',
				],
			]);
		}
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if ($db->fieldExists('sender_role', 'chat_messages')) {
			$this->forge->dropColumn('chat_messages', 'sender_role');
		}

        if ($db->fieldExists('receiver_role', 'chat_messages')) {
			$this->forge->dropColumn('chat_messages', 'receiver_role');
		}
    }
}

?>

