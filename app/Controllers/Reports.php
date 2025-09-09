<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleItemModel;

class Reports extends BaseController
{
    public function index()
    {
        if ($resp = $this->requireRole(['admin'])) return $resp;

        $saleModel = new SaleModel();
        $itemModel = new SaleItemModel();

        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $salesToday = $saleModel->where('DATE(created_at)', $today)->selectSum('total')->first()['total'] ?? 0;
        $profitToday = $saleModel->where('DATE(created_at)', $today)->selectSum('profit')->first()['profit'] ?? 0;

        $salesMonth = $saleModel->where('DATE_FORMAT(created_at, "%Y-%m")', date('Y-m'))->selectSum('total')->first()['total'] ?? 0;
        $profitMonth = $saleModel->where('DATE_FORMAT(created_at, "%Y-%m")', date('Y-m'))->selectSum('profit')->first()['profit'] ?? 0;

        // Top products by qty this month
        $topProducts = $itemModel
            ->select('product_id, SUM(qty) as qty_sold')
            ->where('created_at >=', $monthStart)
            ->groupBy('product_id')
            ->orderBy('qty_sold', 'DESC')
            ->findAll(10);

        return view('reports/index', [
            'salesToday' => (float) $salesToday,
            'profitToday' => (float) $profitToday,
            'salesMonth' => (float) $salesMonth,
            'profitMonth' => (float) $profitMonth,
            'topProducts' => $topProducts,
        ]);
    }
}


