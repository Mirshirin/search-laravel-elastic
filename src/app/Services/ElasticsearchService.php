<?php
namespace App\Services;


use App\Models\Product;

use Illuminate\Http\Request;
use App\Jobs\ReindexProductsJob;
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
        Log::info('Attempting to index product description: ' . $product->description);

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
            // ارسال نتیجه به کلاینت
            return response()->json([
                'message' => 'Product indexed successfully',
                'elasticsearch_response' => $response,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error indexing product: ' . $product->id . '. Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateProduct(Product $product)
    {
        Log::info("Updating product: ");
        Log::info('Product update process started for product id: ' . $product->id);

        $validatedData = request()->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);
    
        $product->update($validatedData);
    
        Log::info('Starting Elasticsearch reindexing for product id: ' . $product->id);

         
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

        if (!$product) {
            // اگر محصول پیدا نشد، یک پیام خطا برمی‌گرداند
            return response()->json(['message' => 'Product not found.'], 404);
        }
        try {
            $response = $this->client->delete($params);
            Log::info('Product successfully deleted from index: ' . $product->id);
            return $response;
        } catch (\Exception $e) {
            Log::error('Error deleting product from index: ' . $product->id . '. Error: ' . $e->getMessage());
            throw $e;
        }
    }
    public function searchProducts($query, $page = 1, $size = 10)
    {
        $from = ($page - 1) * $size;
        $totalResults = 0;
        $products = collect();

        if (empty($query)) {
            $products = Product::all();
            $totalResults = $products->count();
        } else {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();
            
            try {
                $response = $client->ping();
                if ($response) {
                    echo "Connected to Elasticsearch successfully.";
                }
            } catch (\Exception $e) {
                echo "Failed to connect to Elasticsearch: " . $e->getMessage();
            }

            $params = [
                'index' => 'products',
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['name', 'description', 'image_text']
                        ]
                    ],
                    'size' => $size,
                    'from' => $from
                ]
            ];

            $results = $client->search($params);
            $products = collect($results['hits']['hits'])->map(function ($hit) {
                return array_merge($hit['_source'], ['id' => $hit['_id']]);
            });

            $totalResults = $results['hits']['total']['value'];
        }

        return ['products' => $products, 'totalResults' => $totalResults];
    }

}
