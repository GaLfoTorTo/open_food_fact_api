<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Domain\Product\Enums\ProductStatus;
use App\Domain\Product\Models\Product;
use App\Domain\User\Models\User;

class ProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        //RESGATAR USUARIO DE TESTES
        $this->user = User::first();
        $this->apiToken = $this->user->createToken('test-api-key')->plainTextToken;
    }

    //TESTE DE BUSCA DE PRODUTOS
    public function test_can_get_products()
    {
        //COLETAR PRODUTOS
        $product = Product::get()->limit(100);
        if(sizeof($product) == 0){
            $product = Product::factory()->create([
                'status' => ProductStatus::PUBLISHED
            ]);
        }
        
        $response = $this->getJson("/api/products");
        $response->assertStatus(200);
    }

    //TESTE DE BUSCA DE PRODUTO POR CODIGO
    public function test_can_get_product()
    {
        //COLETAR PRODUTOS
        $product = Product::get()->limit(100);
        if(sizeof($product) == 0){
            $product = Product::factory()->create([
                'status' => ProductStatus::PUBLISHED
            ]);
        }
        
        $response = $this->getJson("/api/products/{$product->code}");
        $response->assertStatus(200)->assertJsonPath('code', $product->code);
    }
    
    //TESTE DE ATUALIZAÇÃO DE PRODUTOS
    public function test_can_update_product()
    {
        //COLETAR PRODUTO
        $product = Product::get()->first();
        if(empty($product)){
            $product = Product::factory()->create();
        }
        
        $apiKey = 'test-api-key';
        
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
    {//COLETAR PRODUTO
        $product = Product::get()->first();
        if(empty($product)){
            $product = Product::factory()->create();
        }
        
        $apiKey = 'test-api-key';
        
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
        //COLETAR PRODUTO
        $products = Product::get()->limit(25);
        if(sizeof($product) == 0){
            $product = Product::factory()->count(25)->create();
        }
        
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