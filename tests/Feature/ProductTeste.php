<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Products\Models\Product;
use App\Domain\Products\Enums\ProductStatus;

class ProductTest extends TestCase
{
    //TESTE DE BUSCA DE PRODUTOS
    public function test_can_get_products()
    {
        $product = Product::factory()->create([
            'status' => ProductStatus::PUBLISHED
        ]);
        
        $response = $this->getJson("/api/products");
        $response->assertStatus(200);
    }

    //TESTE DE BUSCA DE PRODUTO POR CODIGO
    public function test_can_get_product()
    {
        $product = Product::factory()->create([
            'status' => ProductStatus::PUBLISHED
        ]);
        
        $response = $this->getJson("/api/products/{$product->code}");
        $response->assertStatus(200)->assertJsonPath('code', $product->code);
    }
    
    //TESTE DE ATUALIZAÇÃO DE PRODUTOS
    public function test_can_update_product()
    {
        $product = Product::factory()->create();
        $apiKey = 'test-api-key'; // Configure no setup
        
        $response = $this->withHeader('X-API-Key', $apiKey)
                         ->putJson("/api/products/{$product->code}", [
                             'product_name' => 'Updated Name'
                         ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'code' => $product->code,
            'product_name' => 'Updated Name'
        ]);
    }
    
    //TESTE DE REMOÇÃO DE PRODUTOS
    public function test_can_delete_product()
    {
        $product = Product::factory()->create();
        $apiKey = 'test-api-key'; // Configure no setup
        
        $response = $this->withHeader('X-API-Key', $apiKey)->deleteJson("/api/products/{$product->code}");
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'code' => $product->code,
            'product_name' => 'Updated Name'
        ]);
    }
    
    //TESTE DE PAGINAÇÃO
    public function test_pagination_works()
    {
        Product::factory()->count(25)->create();
        
        $response = $this->getJson('/api/products?per_page=10');
        
        $response->assertStatus(200)
                ->assertJsonCount(10, 'data')
                ->assertJsonStructure([
                    'current_page',
                    'data',
                    'per_page',
                    'total'
                ]);
    }
}