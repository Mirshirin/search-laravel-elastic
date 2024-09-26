<?php
namespace App\Services;


use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();
    }

    public function indexProduct(Product $product)
    {
        Log::info('Attempting to index product: ' . $product->id);
        Log::info('Attempting to index product name: ' . $product->name);

        $params = [
            'index' => 'products',
            'id'    => $product->id,

            'body'  => [
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'image'       => $product->image,
                'image_text'  => $product->image_text,
            ]
        ];
        try {
            $response = $this->client->index($params);
            Log::info('Product successfully indexed: ' . $product->id);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error indexing product: ' . $product->id . '. Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateProduct(Product $product)
    {
        Log::info("Updating product: ");

        $validatedData = request()->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);
    
        $product->update($validatedData);
    
        if (request()->hasFile('image')) {
            $imagePath = request()->file('image')->store('images');
            $product->image_path = $imagePath;
    
          
        }
    
        $product->save();
         
        // ذخیره محصول در Elasticsearch
        $this->indexProduct($product);
          
        return redirect()->back()->with('success', 'Product created successfully.');
    
    }
    public function deleteProduct(Product $product)
    {
        Log::info('Deleting product from index: ' . $product->id);
        $params = [
            'index' => 'products',
            'id'    => $product->id,
        ];
        try {
            $response = $this->client->delete($params);
            Log::info('Product successfully deleted from index: ' . $product->id);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error deleting product from index: ' . $product->id . '. Error: ' . $e->getMessage());
            throw $e;
        }
    }

}
