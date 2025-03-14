<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products', function(){
    return 'Obteniendo Productos';
});

Route::get('/products{id}', function(){
    return 'Obteniendo un producto';
});

Route::post('/products', function(){
    return 'Creando Productos';
});

Route::put('/products{id}', function(){
    return 'Actualizando producto';
});

Route::delete('/products{id}', function(){
    return 'Eliminar Producto';
});

