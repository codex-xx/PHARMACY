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

    public function exportSales()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $saleModel = new SaleModel();

        // Get sales data for the current month
        $monthStart = date('Y-m-01');
        $sales = $saleModel->where('created_at >=', $monthStart)
                          ->select('id, created_at, total, profit')
                          ->orderBy('created_at', 'DESC')
                          ->findAll();

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['Sale ID', 'Date', 'Total', 'Profit']);

        // CSV data
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale['id'],
                date('Y-m-d H:i:s', strtotime($sale['created_at'])),
                number_format($sale['total'], 2),
                number_format($sale['profit'], 2)
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportInventory()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $productModel = new ProductModel();

        // Get all products
        $products = $productModel->select('id, name, sku, price, stock, reorder_threshold, expiry_date')
                                ->orderBy('name', 'ASC')
                                ->findAll();

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="inventory_report_' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['Product ID', 'Name', 'SKU', 'Price', 'Stock', 'Reorder Threshold', 'Expiry Date']);

        // CSV data
        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['sku'],
                number_format($product['price'], 2),
                $product['stock'],
                $product['reorder_threshold'] ?? 0,
                $product['expiry_date'] ? date('Y-m-d', strtotime($product['expiry_date'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }

    public function exportTopProducts()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $itemModel = new SaleItemModel();
        $productModel = new ProductModel();

        // Get top selling products this month
        $monthStart = date('Y-m-01');
        $topProducts = $itemModel
            ->select('product_id, SUM(qty) as qty_sold, SUM(price * qty) as revenue')
            ->where('created_at >=', $monthStart)
            ->groupBy('product_id')
            ->orderBy('qty_sold', 'DESC')
            ->findAll();

        // Add product names
        foreach ($topProducts as &$prod) {
            $product = $productModel->find($prod['product_id']);
            $prod['name'] = $product['name'] ?? 'Unknown';
            $prod['sku'] = $product['sku'] ?? '';
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="top_products_report_' . date('Y-m') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['Product ID', 'Name', 'SKU', 'Quantity Sold', 'Revenue']);

        // CSV data
        foreach ($topProducts as $product) {
            fputcsv($output, [
                $product['product_id'],
                $product['name'],
                $product['sku'],
                $product['qty_sold'],
                number_format($product['revenue'], 2)
            ]);
        }

        fclose($output);
        exit;
    }
}
