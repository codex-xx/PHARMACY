<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;

class Reports extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $saleModel = new SaleModel();
        $itemModel = new SaleItemModel();
        $productModel = new ProductModel();

        // Sales data
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $monthStart = date('Y-m-01');

        $salesToday = $saleModel->where('DATE(created_at)', $today)->selectSum('total')->first()['total'] ?? 0;
        $salesWeek = $saleModel->where('created_at >=', $weekStart)->selectSum('total')->first()['total'] ?? 0;
        $salesMonth = $saleModel->where('DATE_FORMAT(created_at, "%Y-%m")', date('Y-m'))->selectSum('total')->first()['total'] ?? 0;

        $profitToday = $saleModel->where('DATE(created_at)', $today)->selectSum('profit')->first()['profit'] ?? 0;
        $profitMonth = $saleModel->where('DATE_FORMAT(created_at, "%Y-%m")', date('Y-m'))->selectSum('profit')->first()['profit'] ?? 0;

        // Top sales products this month (by qty sold)
        $topSaleProducts = $itemModel
            ->select('product_id, SUM(qty) as qty_sold, SUM(price * qty) as revenue')
            ->where('created_at >=', $monthStart)
            ->groupBy('product_id')
            ->orderBy('qty_sold', 'DESC')
            ->findAll(10);

        // Add product names to top sale products
        foreach ($topSaleProducts as &$prod) {
            $product = $productModel->find($prod['product_id']);
            $prod['name'] = $product['name'] ?? 'Unknown';
            $prod['revenue'] = (float) $prod['revenue'];
        }

        // Inventory reports
        $soon = date('Y-m-d', strtotime('+30 days'));
        $lowStockProducts = $productModel->where('stock <= reorder_threshold')->orderBy('stock', 'ASC')->findAll(20);
        $expiringProducts = $productModel->where('expiry_date >=', $today)->where('expiry_date <=', $soon)->orderBy('expiry_date', 'ASC')->findAll(20);

        // Total inventory value
        $totalInventoryValue = $productModel->select('SUM(price * stock) as total')->get()->getRowArray()['total'] ?? 0;

        return view('reports/index', [
            'salesToday' => (float) $salesToday,
            'salesWeek' => (float) $salesWeek,
            'salesMonth' => (float) $salesMonth,
            'profitToday' => (float) $profitToday,
            'profitMonth' => (float) $profitMonth,
            'topSaleProducts' => $topSaleProducts,
            'lowStockProducts' => $lowStockProducts,
            'expiringProducts' => $expiringProducts,
            'totalInventoryValue' => (float) $totalInventoryValue,
        ]);
    }
}
