<?php
namespace App\Services;


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
                        'fields' => ['name', 'description', 'price','image']
                    ]
                ]
            ]
        ];

        $results = $client->search($params);
        
        $products = collect($results['hits']['hits'])->map(function ($hit) {
            return $hit['_source']; 
        });

        return view('search_results', ['products' => $products, 'query' => $query]);
    
    }
}
