<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ElasticsearchService;

class ProductControllerApi extends Controller
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function index()
    {
       return response()->json(Product::all(), 200);  
    }   
    
    public function store(Request $request)
    {
        $validatedData = $this->validateProduct($request);
        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }   
   
   // نمایش محصول خاص
    public function show($id)
    {      
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
        return response()->json($product, 200); // برگرداندن اطلاعات محصول
    }

    
    public function update(Request $request, $id)
    {  
        $product = Product::findOrFail($id);

        // اعتبارسنجی داده‌ها
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        // بروزرسانی محصول
        $product->update($validatedData);
        
        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $product
        ], 200);
    }

    // حذف محصول
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204); // پاسخ با کد 204 (بدون محتوا)
    }
     // ایجاد محصول جدید
    private function validateProduct(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'nullable',
        ]);
    }

    public function search(Request $request)    
    {
        $query = $request->input('search');
        $page = $request->input('page', 1);
        $size = 10;
    
        $searchResult = $this->elasticsearchService->searchProducts($query, $page, $size);

        return response()->json([
            'products' => $searchResult['products'],
            'currentPage' => $page,
            'totalResults' => $searchResult['totalResults'],
            'size' => $size
        ]);
      
    }
}
