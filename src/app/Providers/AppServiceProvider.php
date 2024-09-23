<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
