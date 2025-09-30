<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\ProductModel;

class BarcodeController extends ResourceController
{
    protected $format = 'json';

    // ✅ Generate Barcode Image
    public function generate($code = null)
    {
        if (!$code) {
            return $this->fail('No code provided');
        }

        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);

        return $this->response
                    ->setHeader('Content-Type', 'image/png')
                    ->setBody($barcode);
    }

    // ✅ Lookup Product by Barcode
    public function find($barcode = null)
    {
        if (!$barcode) {
            return $this->fail('No barcode provided');
        }

        $productModel = new ProductModel();
        $product = $productModel->where('barcode', $barcode)->first();

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        return $this->respond($product);
    }

    // ✅ Add Product with Auto Barcode
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['name']) || !isset($data['price'])) {
            return $this->failValidationErrors("Name and Price are required");
        }

        $productModel = new ProductModel();

        // Auto-generate a unique barcode (you can customize this logic)
        $barcode = rand(1000000000, 9999999999);

        $data['barcode'] = $barcode;

        $productModel->insert($data);

        return $this->respondCreated([
            'message' => 'Product added successfully',
            'barcode' => $barcode
        ]);
    }
}
