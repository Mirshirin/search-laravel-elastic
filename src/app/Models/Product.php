<?php
namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\ElasticsearchService;
class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price','image'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
   
    
    public static function boot()
    {
        parent::boot();

        // هنگامی که یک محصول ایجاد یا به‌روزرسانی می‌شود، ایندکس‌سازی کنید
        static::updated(function ($product) {
           // dd('updateproductttttttttttttttt');
            app(ElasticSearchService::class)->updateProduct($product);
        });
        self::created(function ($product) {        
            app(ElasticSearchService::class)->indexProduct($product);
        });
        // هنگامی که یک محصول حذف می‌شود، از ایندکس حذف کنید
        static::deleted(function ($product) {
            $product->deleteFromIndex();
        });
    }
    public static function createIndex()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        $products = Product::chunk(100,function ($products) use($client){
            foreach($products as $product){
                $params = [
                    'index' => 'products',
                    'id'    => $product->id,
                    'body'  => [
                        'name'        => $product->name,
                        'description' => $product->description,
                        'price'       => $product->price,
                        'image'       => $product->image,
                        'image_text'  => $product->image_text // متن استخراج شده از تصویر
                    ]
                ];

         $client->index($params);
            }
        });
           
    
       return '';
    }
   

    public function deleteFromIndex()
    {
        app(ElasticSearchService::class)->deleteProduct($this);

        // $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        // $params = [
        //     'index' => 'products',
        //     'id'    => $this->id,
        // ];

        // $client->delete($params);
    }

   
    
}
