<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Elastic\Elasticsearch\ClientBuilder;
use thiagoalessio\TesseractOCR\TesseractOCR;



class ProductController extends Controller
{ 
    public function show()
    {
        $products = Product::all();
        return view('product.search', ['products' => $products]);        
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        if (empty($query)) {
            $products = Product::all();
        }else {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

            try {
                $response = $client->ping();
                if ($response) {
                    echo "Connected to Elasticsearch successfully.";
                }
            } catch (\Exception $e) {
                echo "Failed to connect to Elasticsearch: " . $e->getMessage();
            }
            
            $params = [
                'index' => 'products',
                'body'  => [
                    'query' => [
                        'multi_match' => [
                            'query'  => $query,
                            'fields' => ['name', 'description','image', 'image_text'] 
                            ]
                            ]
                        ]
                    ];
    
            $results= $client->search($params);
    
            $products = collect($results['hits']['hits'])->map(function ($hit) {
                return array_merge($hit['_source'], ['id' => $hit['_id']]);
            });
            
        }      
     
            return view('product.search', ['products' => $products]);
  
    }
  
}
