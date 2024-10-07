<?php
namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use App\Policies\ProductPolicy;
use App\Jobs\ReindexProductsJob;
use App\Services\ProductsService;
use App\Services\ElasticsearchService;
use Elastic\Elasticsearch\ClientBuilder;
use App\Http\Requests\StoreProductRequest;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Contracts\ProductRepositoryInterface;




class ProductController extends Controller
{ 
    protected $productRepository; 
    protected $productsService;
 
    public function __construct(ProductRepositoryInterface $productRepository,ProductsService $productsService)
    {
        $this->productRepository = $productRepository;    
        $this->productsService = $productsService; 

    }   
    public function index(Request $request)
    {
        $searchProduct= new ElasticsearchService();
        $query = $request->input('search');
        $page = $request->input('page', 1);
        $size = 10;

        $searchResult = $searchProduct->searchProducts($query, $page, $size);

        return view('product.search', [
            'products' => $searchResult['products'],
            'currentPage' => $page,
            'totalResults' => $searchResult['totalResults'],
            'size' => $size
        ]);
    } 
    
    public function reindex() {
   
        ReindexProductsJob::dispatch();
        return response()->json(['message' => 'Reindexing started.'], 200);
     }

     public function insert(Request $request)
     {  
         dd($request->all());
         $product=new Product();
         $product->name=$request->name;
         $product->description=$request->description;
         $product->price=$request->price;
         $product->image=$request->image;
         if ($product->save())
         return ['status' => 'product.edit-product'];
     }

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
    public function destroy($id)
    {       
        try {
            $product = $this->productRepository->destroy($id);
    
            // اگر محصول حذف شد، بازگشت پیام موفقیت
            if ($product) {
                return response()->json(['status' => 'Data deleted successfully.']);
            } else {
                return response()->json(['status' => 'Product not found.'], 404);
            }
        } catch (\Exception $e) {
            // مدیریت خطاها و بازگرداندن پیام خطا
            return response()->json(['status' => 'Error deleting data.', 'error' => $e->getMessage()], 500);
        }   
    }

    public function getIndexedProducts()    
    {
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        $params = [
            'index' => 'products',
            'body'  => [
                'query' => [
                    'match_all' => (object)[] // همه محصولات ایندکس‌شده را می‌گیرد
                ],
                'size' => 1000 // می‌توانید تعداد آیتم‌ها را تنظیم کنید
            ]
        ];

        try {
            $response = $client->search($params);

            // چاپ داده‌های ایندکس‌شده
            foreach ($response['hits']['hits'] as $hit) {
                echo "Product ID: " . $hit['_id'] . "\n";
                echo "Name: " . $hit['_source']['name'] . "\n";
                echo "Description: " . $hit['_source']['description'] . "\n";
                echo "Image: " . $hit['_source']['image'] . "\n";
                echo "Image Text: " . $hit['_source']['image_text'] . "\n";
                echo "-------------------\n";
            }
        } catch (\Exception $e) {
            echo "Error retrieving indexed products: " . $e->getMessage();
        }
    }

  
}
