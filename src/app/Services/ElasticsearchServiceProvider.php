<?php
namespace App\Services;


use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;
use App\Contracts\ProductSearchInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ElasticsearchServiceProvider implements ProductSearchInterface
{
    public function search(string $query)
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        $params = [
            'index' => 'products',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['name', 'description','image']
                    ]
                ]
            ]
        ];

        $results = $client->search($params);
        
        // $products = collect($results['hits']['hits'])->map(function ($hit) {
        //     return array_merge($hit['_source'], ['id' => $hit['_id']]);
        // }); 
       
       // $products= $results['hits']['hits'];
       $products= $results ;
        //   //  return array_merge($products['_source'], ['id' => $products['_id']]);
        
       

      return view('product.search', ['products' => $products, 'query' => $query]);
  
    }
}
