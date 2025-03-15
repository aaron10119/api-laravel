<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
                'regex:/^[a-zA-Z0-9 ]+$/',
            ],
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ], [
            'name.regex' => 'El nombre del producto no debe contener caracteres especiales. Solo se permiten letras, números y espacios.',
        ]);
    
        $existe = Product::where('name', $validated['name'])
                         ->where('category_id', $validated['category_id'])
                         ->exists();
    
        if ($existe) {
            return response()->json(['message' => 'Producto agregado'], 201);
        }
    
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
        if (Cache::has("deleted_product_$id")) {
            return response()->json(['message' => 'Este producto ya fue eliminado recientemente.'], 200);
        }
    
        $product = Product::find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    
        Cache::put("deleted_product_$id", true, now()->addSeconds(2));
    
        $productName = $product->name;
        $product->delete();
    
        return response()->json(['message' => "Producto '$productName' eliminado"], 200);
    }
    

    
}
