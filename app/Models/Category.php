<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     title="Categoría",
 *     description="Modelo de Categoría",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electrónica")
 * )
 */
class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    protected $fillable = 
    [
        'name'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
