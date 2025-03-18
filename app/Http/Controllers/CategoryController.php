<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(name="Categorías", description="Operaciones con categorías")
 */
class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Listar todas las categorías",
     *     tags={"Categorías"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Category"))
     *     )
     * )
     */
    public function index()
    {
        $categories = Category::all(); 
        return response()->json($categories);
    }

        /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Obtener una categoría por ID",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category);
        }
        return response()->json(['message' => 'Categoria no encontrada'], 404);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Crear una nueva categoría",
     *     tags={"Categorías"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Tecnología")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error en la validación"
     *     )
     * )
     */
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

     /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Actualizar una categoría",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Deportes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Eliminar una categoría",
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada con éxito"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (Cache::has("deleted_category_$id")) {
            return response()->json(['message' => 'Esta categoría ya fue eliminada recientemente.'], 200);
        }
    
        $category = Category::find($id);
    
        if (!$category) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
    
        if ($category->products()->exists()) {
            return response()->json(['message' => 'No se puede eliminar la categoría porque tiene productos asociados.'], 400);
        }
    
        Cache::put("deleted_category_$id", true, now()->addSeconds(2));
    
        $categoryName = $category->name;
        $category->delete();
    
        return response()->json(['message' => "Categoría '$categoryName' eliminada"], 200);
    }
    
    

}
