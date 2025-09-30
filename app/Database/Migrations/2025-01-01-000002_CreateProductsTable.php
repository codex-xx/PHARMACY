<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
   $this->forge->addField([
    'id' => [
        'type'           => 'INT',
        'constraint'     => 11,
        'unsigned'       => true,
        'auto_increment' => true,
    ],
    'name' => [
        'type'       => 'VARCHAR',
        'constraint' => '150',
    ],
    'sku' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'null'       => true,
    ],
    'barcode' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'unique'     => true,
        'null'       => true,
    ],
    'price' => [
        'type'       => 'DECIMAL',
        'constraint' => '10,2',
        'default'    => '0.00',
    ],
    'stock' => [
        'type'       => 'INT',
        'constraint' => 11,
        'default'    => 0,
    ],
    'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ],
    'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
    ],
]);

$this->forge->addKey('id', true);
$this->forge->addKey('sku');   // keep this
// no need to addKey('barcode')
$this->forge->createTable('products');

    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
