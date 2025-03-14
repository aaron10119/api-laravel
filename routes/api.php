<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//CATEGORIAS
Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::post('/categories', [CategoryController::class, 'store']);

Route::put('/categories/{id}', [CategoryController::class, 'update']);

Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);




//PRODUCTOS

Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/categories', [ProductController::class, 'indexcategory']);

Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/products', [ProductController::class, 'store']);

Route::put('/products/{id}', [ProductController::class, 'update']);

Route::delete('/products/{id}', [ProductController::class, 'destroy']);


