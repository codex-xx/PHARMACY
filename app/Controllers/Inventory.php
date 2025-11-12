<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Inventory extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin', 'cashier'])) return $resp;

        $model = new ProductModel();
        $today = date('Y-m-d');
        $soon = date('Y-m-d', strtotime('+30 days'));

        $lowStock = $model->where('stock <= reorder_threshold')->orderBy('stock', 'ASC')->findAll(50);
        $expiring = $model->where('expiry_date >=', $today)->where('expiry_date <=', $soon)->orderBy('expiry_date', 'ASC')->findAll(50);
        $allProducts = $model->orderBy('name', 'ASC')->findAll();

        return view('inventory/index', [
            'lowStock' => $lowStock,
            'expiring' => $expiring,
            'allProducts' => $allProducts,
        ]);
    }
}


