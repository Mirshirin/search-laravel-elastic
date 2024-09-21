<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Queue\SerializesModels;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;


class ReindexProducts implements ShouldQueue
{
    use Queueable;
    protected $client;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->client = app('Elasticsearch');

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    
        $products = Product::all();

        foreach ($products as $product) {
            $product->indexToElasticsearch(); 
        }
    
    }
}
