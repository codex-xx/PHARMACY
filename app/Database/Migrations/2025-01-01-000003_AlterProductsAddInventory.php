<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterProductsAddInventory extends Migration
{
    public function up()
    {
        $fields = [
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'stock',
            ],
            'reorder_threshold' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 10,
                'after' => 'expiry_date',
            ],
        ];
        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', ['expiry_date', 'reorder_threshold']);
    }
}


