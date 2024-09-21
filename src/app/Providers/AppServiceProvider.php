<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;
use App\Contracts\ProductSearchInterface;
use App\Services\ElasticsearchServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductSearchInterface::class,ElasticsearchServiceProvider::class);
        $this->app->singleton('Elasticsearch', function () {
                return ClientBuilder::create()
                    ->setHosts(config('services.elasticsearch.hosts'))
                    ->build();
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
