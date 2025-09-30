<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Paracetamol 500mg', 'sku' => 'PCM500', 'price' => 2.50, 'stock' => 100, 'barcode' => '123456789011'],
            ['name' => 'Ibuprofen 200mg', 'sku' => 'IBU200', 'price' => 3.00, 'stock' => 80, 'barcode' => '123456789012'],
            ['name' => 'Amoxicillin 500mg', 'sku' => 'AMX500', 'price' => 8.00, 'stock' => 50, 'barcode' => '123456789013'],
            ['name' => 'Cough Syrup', 'sku' => 'CSYRUP', 'price' => 4.75, 'stock' => 60, 'barcode' => '123456789014'],
            ['name' => 'Vitamin C 1000mg', 'sku' => 'VITC1K', 'price' => 6.20, 'stock' => 70, 'barcode' => '123456789015'],
            ['name' => 'Antiseptic Cream', 'sku' => 'ANTCR', 'price' => 5.30, 'stock' => 40, 'barcode' => '123456789016'],
        ];

        $builder = $this->db->table('products');

        foreach ($products as $p) {
            $existing = $builder->getWhere(['sku' => $p['sku']])->getRow();

            $row = array_merge($p, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($existing) {
                $builder->where('id', $existing->id)->update($row);
            } else {
                $builder->insert($row);
            }
        }
    }
}
