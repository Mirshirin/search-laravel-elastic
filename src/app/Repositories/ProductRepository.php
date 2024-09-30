<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use App\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Product::all();
    }
    public function getProductById($id)
    {
        return Product::findorfail($id);
    }
    public function store(array $data, $imageFile = null)
    {
        try {
            $product = new Product($data);
   
            // بررسی و ذخیره تصویر در صورت وجود
            if ($imageFile && $imageFile instanceof \Illuminate\Http\UploadedFile) {
        
                $imageName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $path = $imageFile->storeAs('photos', $imageName, 'public');        
                $product->image = $path;
           
            }
    
            // ذخیره محصول در دیتابیس
            $product->save();
            
    
            return $product;
        } catch (\Exception $e) {
            throw new \Exception('Failed to store product: ' . $e->getMessage());
        }
    }

    public function update($id, array $data, $imageFile =null)
    {    
         
        $product = $this->getProductById($id);
        if (!$product) {
            throw new \Exception('Product not found.'); 
        }
        
        if ($imageFile!== null) {
            if ($product->image) {
            $oldImage = $product->image;
            if (Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            $imageName = uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imagePath = $imageFile->storeAs('photos', $imageName, 'public');
            $data['image'] = $imagePath; // ذخیره مسیر جدید در دیتابیس
            }
        }
  
        $product->update($data);
        
        return $product;    
    }


    public function destroy($id)
    {
        $product = $this->getProductById($id);
        if (!$product) {
            return back()->withErrors(['error' => 'Product not found.']);
        }
    
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
    
        $product->delete();    
        return redirect()->route('products.index');
    }
}
