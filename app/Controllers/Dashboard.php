<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin', 'cashier'])) return $resp;
        $user = $this->currentUser();
        // Load products but do not fail the page if table is missing or DB error occurs
        $products = [];
        try {
            $productModel = new \App\Models\ProductModel();
            $products = $productModel->orderBy('name', 'ASC')->findAll(50);
        } catch (\Throwable $e) {
            // Fallback to empty list; page will still render
            $products = [];
        }

        return view('dashboard', [
            'username' => $user['username'] ?? 'User',
            'dbProducts' => $products,
        ]);
    }
}


