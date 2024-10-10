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
    
    
}
