<?php
namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use App\Policies\ProductPolicy;
use App\Jobs\ReindexProductsJob;
use App\Services\ProductsService;
use Elastic\Elasticsearch\ClientBuilder;
use App\Http\Requests\StoreProductRequest;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Contracts\ProductRepositoryInterface;




class ProductController extends Controller
{ 
    protected $productRepository;  
    public function __construct(ProductRepositoryInterface $productRepository,ProductsService $productsService)
    {
        $this->productRepository = $productRepository;    
        $this->productsService = $productsService; 


    }

    public function show()
    {
        $products = Product::all();
        return view('product.index', ['products' => $products]);        
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
                            'fields' => ['name', 'description','image', 'image_text','price'] 
                            ]
                            ]
                        ]
                    ];
    
            $results= $client->search($params);
                    //     dd($results);
            $products = collect($results['hits']['hits'])->map(function ($hit) {
                return array_merge($hit['_source'], ['id' => $hit['_id']]);
            });
            
        }      
     
            return view('product.search', ['products' => $products]);
  
    }
    public function reindex() {
   
      // $elasticsearchService = app(ElasticsearchService::class);
       ReindexProductsJob::dispatch();

        return response()->json(['message' => 'Reindexing started.'], 200);
     }
     protected $productsService;

     public function create()
     {
        
         return view('product.create-product');
     }
    public function store(Request $request)
    {  
       $product = $this->productsService->create($request->all());
        return response()->json(['message' => 'Product added successfully.', 'product' => $product]);
    }
    public function edit($id)
    {
        $product = $this->productRepository->getProductById($id);
        return view('product.edit-product', ['product' => $product]);
    }
    
    public function update(Request $request, $id)
    {
       
            // $validatedData = $request->validated();  
            if (!empty($request->image))     
                $image = $request->file('image')->store('photos', 'public'); 
            else {
                $image =null;
            }       
            $product = app(ProductRepositoryInterface::class)->update($id, $request->all(), $image);    
            return redirect(route('products.index'));
         }
  
}
