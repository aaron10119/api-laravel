# API REST Laravel

Esta api esta hecha en laravel

## Pasos para iniciar

### 1. Clona el repositorio de Git:
```
git clone <URL_DEL_REPOSITORIO>
```

### 2. Ejecuta el proyecto con el siguiente comando:
```
php artisan serve
```

### 3. La base de datos que yo use es en Mysql y esta dentro del repositorio pero si se usara otro gestor como SQL

Cree la base de datos init

```
CREATE DATABASE init;
```

 y ejecute la migracion categories y products, en su respectiva base de datos con su coneccion en el archivo .env

```
php artisan migrate
```

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=init
DB_USERNAME=root
DB_PASSWORD=
```

```
php artisan migrate
```

### 4. Tuve problemas con el certificado CORS y modifique el archivo config\cors.php con mi ruta de Vuejs:
```
return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:8081'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
```

### 5. El ejercicio uno de la prueba para validar los caracteres de la categoria y productos

○ Si type es "product", el campo value no debe contener caracteres especiales, solo letras, números y espacios. 

○ Si type es "category", el campo value debe ser una palabra con letras únicamente (sin números ni caracteres especiales).


Podemos validar ese ejercicio en app\Http\Controllers\CategoryController.php y en app\Http\Controllers\ProductsController.php
```
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
```

```
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
```


NOTA: Se estarán realizando cambios en los commits para encontrar errores, mejorar el código o agregar contenido adicional al proyecto.


### Por Aaron Hernandez Bueno