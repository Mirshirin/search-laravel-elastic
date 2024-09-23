<?php
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elastic\Elasticsearch\ClientBuilder;

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
    public static function createIndex()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();
        $params = [
            'index' => 'products',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'name' => ['type' => 'text'],
                        'description' => ['type' => 'text'],
                        'price' => ['type' => 'text'],
                        'image' => ['type' => 'text'], // ذخیره مسیر تصویر
                        'image_text' => ['type' => 'text'], // ذخیره متن استخراج شده از تصویر 
                    ],
                ]
            ],
        ];

        try {
            $client->indices()->create($params);
        } catch (\Exception $e) {
            dd($e);
            // Handle the exception (e.g., log the error or display a user-friendly message)
        }
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
                'image_text'  => $this->image_text, // متن استخراج شده از تصویر


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

   
    public function updateElasticsearchIndex(Request $request)
    {
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->image = $request->file('image')->store('images');
    
        // استخراج متن از تصویر
        $imagePath = Storage::disk('local')->path($product->image);
        $product->image_text = $this->extractImageText($imagePath);
    
        $product->save();
    
        // ذخیره محصول در Elasticsearch
        $this->indexProductInElasticsearch($product);
    
        return redirect()->back()->with('success', 'Product created successfully.');
    
    }
}
