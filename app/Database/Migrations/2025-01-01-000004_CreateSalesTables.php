<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTables extends Migration
{
    public function up()
    {
        // sales
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'user_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'total' => [ 'type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00' ],
            'profit' => [ 'type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00' ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('sales');

        // sale_items
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'sale_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'product_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'qty' => [ 'type' => 'INT', 'constraint' => 11, 'default' => 1 ],
            'price' => [ 'type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00' ],
            'cost' => [ 'type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00' ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sale_id');
        $this->forge->createTable('sale_items');
    }

    public function down()
    {
        $this->forge->dropTable('sale_items');
        $this->forge->dropTable('sales');
    }
}


