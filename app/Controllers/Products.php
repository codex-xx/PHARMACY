<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;

class Products extends BaseController
{
    protected function ensureAuthenticated(): ?RedirectResponse
    {
        $user = session()->get('user');
        if (!is_array($user) || empty($user['logged_in'])) {
            return redirect()->to('/login');
        }
        return null;
    }

    public function index()
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        $model = new ProductModel();
        $products = $model->orderBy('name', 'ASC')->findAll();
        return view('products/index', ['products' => $products]);
    }

    public function create()
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        return view('products/create');
    }

    public function store(): RedirectResponse
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        $model = new ProductModel();
        $data = [
            'name' => (string) $this->request->getPost('name'),
            'sku' => (string) $this->request->getPost('sku'),
            'price' => (float) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
        ];
        $model->insert($data);
        return redirect()->to('/products');
    }

    public function edit($id)
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        $model = new ProductModel();
        $product = $model->find($id);
        if (!$product) return redirect()->to('/products');
        return view('products/edit', ['product' => $product]);
    }

    public function update($id): RedirectResponse
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        $model = new ProductModel();
        $data = [
            'name' => (string) $this->request->getPost('name'),
            'sku' => (string) $this->request->getPost('sku'),
            'price' => (float) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
        ];
        $model->update($id, $data);
        return redirect()->to('/products');
    }

    public function delete($id): RedirectResponse
    {
        if ($resp = $this->ensureAuthenticated()) return $resp;
        $model = new ProductModel();
        $model->delete($id);
        return redirect()->to('/products');
    }
}


