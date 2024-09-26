<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\ElasticsearchService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class ReindexProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    private $elasticsearchService;

    // public function __construct()
    // {
    //     $this->elasticsearchService = app(ElasticsearchService::class);
    // }

    public function handle()
    {
        $this->elasticsearchService = app(ElasticsearchService::class);

        Log::info('Starting reindexing');
        $products = Product::all();

        foreach ($products as $product) {
            Log::info('Indexing product: ' . $product->id);
            Log::info('Indexing product fullllllll: ' . $product);
            $this->elasticsearchService->indexProduct($product);
        }

        Log::info('Reindexing completed');
    }
}
