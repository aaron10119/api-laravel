<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();

        return response()->json($products,200);
    }

    public function indexcategory()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if ($product) {
            return response()->json($product);
        }
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/',  // Solo letras, números y espacios
            ],
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ], [
            'name.regex' => 'El nombre del producto no debe contener caracteres especiales. Solo se permiten letras, números y espacios.',
        ]);
    
        $product = Product::create([
            'name' => $validated['name'],
            'stock' => $validated['stock'],
            'category_id' => $validated['category_id'],
        ]);
    
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
    
        if ($product) {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z0-9 ]+$/',  // Solo letras, números y espacios
                ],
                'stock' => 'required|integer',
                'category_id' => 'required|exists:categories,id',
            ], [
                'name.regex' => 'El nombre del producto no debe contener caracteres especiales. Solo se permiten letras, números y espacios.',
            ]);
    
            $product->update($validated);
    
            return response()->json($product);
        }
    
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }
    
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Producto Eliminado']);
        }
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }
}
