<?php

namespace App\Providers;

use App\Services\ElasticsearchService;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;
use App\Contracts\ProductRepositoryInterface;
use Elastic\Elasticsearch\Response\Elasticsearch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(ElasticsearchService::class, function ($app) {
                  $client = ClientBuilder::create()
                  ->setHosts(config('services.elasticsearch.hosts'))
                  ->build();
                return new ElasticsearchService($client);
            });
        
    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
