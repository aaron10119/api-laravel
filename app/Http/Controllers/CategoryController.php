<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

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
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $category->update($validated);
            return response()->json($category);
        }
        return response()->json(['message' => 'Categoria no encontrada'], 404);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Categoria eliminada']);
        }
        return response()->json(['message' => 'Categoria no encontrada'], 404);
    }
}
