<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Services\ElasticsearchServiceProvider;


Route::get('/', function () {
    return view('welcome');
});
Route::get('products', [ProductController::class,'show'])->name('show');
Route::get('product', [ProductController::class,'index'])->name('product.index');
