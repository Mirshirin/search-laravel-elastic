<?php

namespace App\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAllProducts();
    public function getProductById($id);
    public function store(array $data, $imageFile = null);
    public function update($id, array $data , $imageFile = null);
    public function destroy($id);
}
