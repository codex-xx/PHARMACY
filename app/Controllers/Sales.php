<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sales extends BaseController
{
    public function checkout()
    {
        // Expect JSON payload: { items: [{id, qty, price}], total }
        $data = $this->request->getJSON(true);
        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid payload']);
        }

    $db = \Config\Database::connect();
        $db->transStart();

        try {
            $saleModel = new SaleModel();
            $itemModel = new SaleItemModel();
            $productModel = new ProductModel();

            $user = session()->get('user');
            $userId = is_array($user) && isset($user['id']) ? $user['id'] : null;

            $total = 0;
            foreach ($data['items'] as $it) {
                $total += ((float) $it['price']) * ((int) $it['qty']);
            }

            $saleId = $saleModel->insert([
                'user_id' => $userId,
                'total' => $total,
                'profit' => 0.00,
            ]);

            if (!$saleId) throw new \Exception('Failed to create sale');

            foreach ($data['items'] as $it) {
                $product = $productModel->find((int)$it['id']);
                if (!$product) continue;

                $qty = (int) $it['qty'];
                $price = (float) $it['price'];

                $itemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => $product['id'],
                    'qty' => $qty,
                    'price' => $price,
                    'cost' => $product['cost'] ?? 0.00,
                ]);

                // Decrement product stock
                $newStock = max(0, ((int)$product['stock']) - $qty);
                $productModel->update($product['id'], ['stock' => $newStock]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Transaction failed']);
            }

            return $this->response->setStatusCode(201)->setJSON(['message' => 'Sale recorded', 'sale_id' => $saleId]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}
