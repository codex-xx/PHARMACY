<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin', 'cashier'])) return $resp;
        $user = $this->currentUser();
        $role = $user['role'] ?? '';

        // Load products but do not fail the page if table is missing or DB error occurs
        $products = [];
        try {
            $productModel = new \App\Models\ProductModel();
            $products = $productModel->orderBy('name', 'ASC')->findAll(50);
        } catch (\Throwable $e) {
            // Fallback to empty list; page will still render
            $products = [];
        }

        $adminData = [];
        if ($role === 'admin') {
            try {
                // Total inventory value
                $totalInventory = $productModel->select('SUM(price * stock) as total')->get()->getRowArray()['total'] ?? 0;

                // Today's sales
                $saleModel = new \App\Models\SaleModel();
                $today = date('Y-m-d');
                $todaySales = $saleModel->where('DATE(created_at)', $today)->selectSum('total')->first()['total'] ?? 0;

                // Low stock count
                $lowStock = $productModel->where('stock <= reorder_threshold')->countAllResults();

                // Items expiring soon (30 days)
                $soon = date('Y-m-d', strtotime('+30 days'));
                $expiring = $productModel->where('expiry_date >=', date('Y-m-d'))->where('expiry_date <=', $soon)->countAllResults();

                $adminData = [
                    'totalInventory' => (float) $totalInventory,
                    'todaySales' => (float) $todaySales,
                    'lowStock' => (int) $lowStock,
                    'expiring' => (int) $expiring,
                ];
            } catch (\Throwable $e) {
                // Silent fallback
                $adminData = ['totalInventory' => 0, 'todaySales' => 0, 'lowStock' => 0, 'expiring' => 0];
            }
        }

        return view('dashboard', array_merge([
            'username' => $user['username'] ?? 'User',
            'dbProducts' => $products,
            'role' => $role,
        ], $adminData));
    }
}
