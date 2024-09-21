<?php
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price','image'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public static function boot()
    {
        parent::boot();

        // هنگامی که یک محصول ایجاد یا به‌روزرسانی می‌شود، ایندکس‌سازی کنید
        static::saved(function ($product) {
            $product->indexToElasticsearch();
        });

        // هنگامی که یک محصول حذف می‌شود، از ایندکس حذف کنید
        static::deleted(function ($product) {
            $product->deleteFromIndex();
        });
    }
    public function search(Request $request)
    {
        $query = $request->input('search');
    
        $params = [
            'index' => 'products',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['name', 'description', 'price','image']
                    ]
                ]
            ]
        ];

        $results = $this->client->search($params);
        
        $products = collect($results['hits']['hits'])->map(function ($hit) {
            return $hit['_source']; 
        });

        return view('search_results', ['products' => $products, 'query' => $query]);
    
    }
    public function indexToElasticsearch()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        $params = [
            'index' => 'products',
            'id'    => $this->id,
            'body'  => [
                'name'        => $this->name,
                'description' => $this->description,
                'price'       => $this->price,
                'image'       => $this->image,

            ]
        ];

        $client->index($params);
    }

    public function deleteFromIndex()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        $params = [
            'index' => 'products',
            'id'    => $this->id,
        ];

        $client->delete($params);
    }

    public function extractImageText($imagePath)
    {
        $ocr = new TesseractOCR($imagePath);
        return $ocr->run();
    }
    public function updateElasticsearchIndex()
    {
        $this->indexToElasticsearch();
    }
}
