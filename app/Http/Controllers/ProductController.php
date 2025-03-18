<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
        /**
     * @OA\Get(
     *     path="/products",
     *     summary="Obtener todos los productos",
     *     tags={"Productos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos obtenida correctamente"
     *     )
     * )
     */
    public function index(){
        $products = Product::all();

        return response()->json($products,200);
    }

        /**
     * @OA\Get(
     *     path="/products-with-category",
     *     summary="Obtener productos con su categoría",
     *     tags={"Productos"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos con su categoría obtenida correctamente"
     *     )
     * )
     */
    public function indexcategory()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

     /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Obtener un producto por ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto obtenido correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if ($product) {
            return response()->json($product);
        }
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","stock","category_id"},
     *             @OA\Property(property="name", type="string", example="Producto A"),
     *             @OA\Property(property="stock", type="integer", example=10),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado correctamente"
     *     ),
     *     @OA\Response(
     *         response=400,
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

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Actualizar un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","stock","category_id"},
     *             @OA\Property(property="name", type="string", example="Producto Modificado"),
     *             @OA\Property(property="stock", type="integer", example=15),
     *             @OA\Property(property="category_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
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
    
    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Eliminar un producto",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del producto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado correctamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
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
