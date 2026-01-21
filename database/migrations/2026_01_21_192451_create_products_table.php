<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum("status",["draft","trash","published"])->default("draft");
            $table->dateTime("imported_t");
            $table->dateTime("created_t");
            $table->dateTime("last_modified_t");
            $table->string("url");
            $table->string("creator");
            $table->string("product_name");
            $table->string("quantity");
            $table->string("brands");
            $table->text("categories")->nullable();
            $table->text("labels")->nullable();
            $table->string("cities")->nullable();
            $table->string("purchase_places")->nullable();
            $table->string("stores")->nullable();
            $table->text("ingredients_text")->nullable();
            $table->string("traces")->nullable();
            $table->string("serving_size")->nullable();
            $table->string("serving_quantity")->nullable();
            $table->string("nutriscore_score")->nullable();
            $table->string("nutriscore_grade")->nullable();
            $table->string("main_category");
            $table->text("image_url")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
