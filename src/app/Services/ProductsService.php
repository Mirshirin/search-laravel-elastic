<?php

namespace App\Services;

use App\Models\Product;

class ProductsService
{
    public function create(array $data): Product
    {
        return Product::create([
            'name' => $data['name'] , 
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'] ?? null,
        ]);
    }
}
