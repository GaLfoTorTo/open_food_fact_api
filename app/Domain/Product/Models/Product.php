<?php

namespace App\Domain\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Product\Enums\ProductStatusEnum;

class Product extends Model
{
    
    protected $table = "products";
    protected $collection = 'products';
    public $timestamps = false;
    protected $fillable = [
        "code",
        "status",
        "url",
        "creator",
        "imported_t",
        "created_t",
        "last_modified_t",
        "product_name",
        "quantity",
        "brands",
        "categories",
        "labels",
        "cities",
        "purchase_places",
        "stores",
        "ingredients_text",
        "traces",
        "serving_size",
        "serving_quantity",
        "nutriscore_score",
        "nutriscore_grade",
        "main_category",
        "image_url",
    ];

    protected $casts = [
        "imported_t" => "datetime",
        "created_t" => "datetime",
        "last_modified_t" => "datetime",
        'status' => ProductStatusEnum::class,
    ];
}
