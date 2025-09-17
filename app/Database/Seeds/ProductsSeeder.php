<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $products = [
            ['name' => 'Paracetamol 500mg', 'sku' => 'PARA-500', 'price' => 2.50, 'stock' => 120, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ibuprofen 200mg', 'sku' => 'IBU-200', 'price' => 3.00, 'stock' => 80, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vitamin C 1000mg', 'sku' => 'VITC-1000', 'price' => 5.00, 'stock' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cough Syrup 100ml', 'sku' => 'COUGH-100', 'price' => 4.20, 'stock' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Amoxicillin 500mg', 'sku' => 'AMOX-500', 'price' => 6.75, 'stock' => 30, 'created_at' => $now, 'updated_at' => $now],
        ];

        $builder = $this->db->table('products');
        foreach ($products as $p) {
            $exists = $builder->where('sku', $p['sku'])->get()->getRowArray();
            if (!$exists) {
                $builder->insert($p);
            }
        }
    }
}

?>

