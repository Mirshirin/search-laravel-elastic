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
        $this->client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'elasticsearch:9200')])->build();
    }
    public function createIndexIfNotExists($indexName, $settings = [], $mappings = [])
    {
        $response = $this->client->ping();
            if ($response) {
                return 'Elasticsearch is running!';
            } else {
                return 'Cannot connect to Elasticsearch!';
            }
        // بررسی وجود ایندکس
        if (!$this->client->indices()->exists(['index' => $indexName])) {
            // ساختن ایندکس
            $params = [
                'index' => $indexName,
                'body' => [
                    'settings' => $settings,
                    'mappings' => $mappings,
                ],
            ];
            $this->client->indices()->create($params);
        }
    }
    
    public function indexProduct(Product $product)
    {
        Log::info('Attempting to index product: ' . $product->id);
        Log::info('Attempting to index product name: ' . $product->name);
        $imageText = pathinfo($product->image, PATHINFO_FILENAME); // تبدیل نام فایل تصویر به متن

        $params = [
            'index' => 'products',
            'id'    => $product->id,
            'body'  => [
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'image'       => $product->image,
                'image_text'  =>  $imageText ,
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
    
     
         
        // ذخیره محصول در Elasticsearch
        app(ElasticSearchService::class)->indexProduct($product);
    

          
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
