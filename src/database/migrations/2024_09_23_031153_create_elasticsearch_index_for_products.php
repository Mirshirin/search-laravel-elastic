<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;
use Elastic\Elasticsearch\ClientBuilder;
use App\Models\Product;

class CreateElasticsearchIndexForProducts extends Migration
{
    public function up()
    {
        // هنگام اجرای migration، ایندکس Elasticsearch ایجاد می‌شود
        Product::createIndex();
    }

    public function down()
    {
        // حذف ایندکس در صورت rollback
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();
        $client->indices()->delete(['index' => 'products']);
    }
}
