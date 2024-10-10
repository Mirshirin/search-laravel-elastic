<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Services\ElasticsearchServiceProvider;


Route::get('/', function () {
    return view('welcome');
});
Route::resource('products',ProductController::class);
Route::post('/products/reindex', [ProductController::class, 'reindex'])->name('products.reindex');
Route::post('/products/getindex', [ProductController::class, 'getIndexedProducts'])->name('products.getIndex');