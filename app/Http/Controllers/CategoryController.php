<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(); 
        return response()->json($categories);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category);
        }
        return response()->json(['message' => 'Categoria no encontrada'], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z]+$/',  // Solo letras
            ],
        ], [
            'name.regex' => 'El nombre de la categoría solo puede contener letras, no se permiten números ni espacios.',
        ]);
    
        // Verificar si la categoría ya existe
        $existe = Category::where('name', $validated['name'])->exists();
        
        if ($existe) {
            return response()->json([
                'message' => 'Categoria agregada.'
            ], 201);
        }
    
        $category = Category::create([
            'name' => $validated['name'],
        ]);
    
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
    
        if ($category) {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z]+$/',  // Solo letras
                ],
            ], [
                'name.regex' => 'El nombre de la categoría solo puede contener letras, no se permiten números ni espacios.',
            ]);
    
            $category->update($validated);
    
            return response()->json($category);
        }
    
        return response()->json(['message' => 'Categoría no encontrada'], 404);
    }


    public function destroy($id)
    {
        if (Cache::has("deleted_category_$id")) {
            return response()->json(['message' => 'Esta categoría ya fue eliminada recientemente.'], 200);
        }
    
        $category = Category::find($id);
    
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    
        Cache::put("deleted_category_$id", true, now()->addSeconds(2));
    
        $categoryName = $category->name;
        $category->delete();
    
        return response()->json(['message' => "Categoría '$categoryName' eliminada"], 200);
    }
    
    

}
