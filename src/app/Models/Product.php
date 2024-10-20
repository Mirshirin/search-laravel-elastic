<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\ElasticsearchService;
class Product extends Model
{
    use HasFactory;
    public $timestamps=true;
    protected $fillable = ['name', 'description', 'price','image'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
   
    
    public static function boot()
    {
        parent::boot();

        // Inject the Elasticsearch service using the app() function
        $elasticsearchService = app(ElasticsearchService::class);

        self::created(function ($product) use ($elasticsearchService) {
            $elasticsearchService->indexProduct($product);
        });

        self::updated(function ($product) use ($elasticsearchService) {
            $elasticsearchService->indexProduct($product);
        });

        self::deleted(function ($product) use ($elasticsearchService) {
            $elasticsearchService->deleteProduct($product);
        });

    }
    public static function createIndex()
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

         // بررسی اینکه آیا ایندکس وجود دارد یا خیر
            if (!$client->indices()->exists(['index' => 'products'])) {
                // اگر ایندکس وجود ندارد، آن را ایجاد کن
                $mapping = [
                    'properties' => [
                        'id' => ['type' => 'keyword'],
                        'name' => ['type' => 'text'],
                        'description' => ['type' => 'text'],
                        'price' => ['type' => 'float'],
                        'image' => ['type' => 'keyword'],
                        'image_text' => ['type' => 'text'],
                    ]
                ];
                
                $client->indices()->create([
                    'index' => 'products',
                    'body' => $mapping
                ]);
            }

        // اگر ایندکس وجود دارد، داده‌ها را به آن اضافه کن
        $params = [
            'index' => 'products',
            'id'    => 'all-products',
            'body'  => [
                'size' => 0,
                'aggs' => [
                    'products' => [
                        'terms' => [
                            'field' => 'id.keyword'
                        ]
                    ]
                ]
            ]
        ];

            $response = $client->search($params);

            foreach ($response['aggregations']['products']['buckets'] as $product) {

                $productData = Product::find($product['key']);
                if ($productData) {
                    $imageText = pathinfo($productData->image, PATHINFO_FILENAME); 

                    $params = [
                        'index' => 'products',
                        'id'    => $productData->id,
                        'body'  => [
                            'name'        => $productData->name,
                            'description' => $productData->description,
                            'price'       => $productData->price,
                            'image'       => $productData->image,
                            'image_text'  =>  $imageText 
                        ]
                    ];

                    $client->update($params);
                }
            }

        return '';
    }

    public function deleteFromIndex()
    {
        app(ElasticSearchService::class)->deleteProduct($this);        
    }

    
}
