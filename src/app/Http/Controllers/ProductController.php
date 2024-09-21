<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;
use App\Contracts\ProductSearchInterface;

class ProductController extends Controller
{
    protected $productSearch;

    // استفاده از Dependency Injection برای تزریق سرویس جستجو
    public function __construct(ProductSearchInterface $productSearch)
    {
        $this->productSearch = $productSearch;
    }

    public function index(Request $request)
    {
        $query = $request->input('search');
        $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])->build();

        try {
            $response = $client->ping();
            if ($response) {
                echo "Connected to Elasticsearch successfully.";
            }
        } catch (\Exception $e) {
            echo "Failed to connect to Elasticsearch: " . $e->getMessage();
        }
        
        if ($query) {
            // جستجو با Elasticsearch
            $products = $this->productSearch->search($query);
        } else {
            // نمایش همه محصولات به صورت پیش‌فرض (اگر جستجویی انجام نشده)
            $products = Product::all();
        }

        return view('product.search', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}
