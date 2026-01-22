<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Product\Enums\ProductStatusEnum;
use App\Domain\Product\Models\Product;
use App\Domain\User\Models\User;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        
        //RESGATAR USUARIO DE TESTES
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->apiToken = $this->user->createToken('test-api-key')->plainTextToken;
        //CRIAR PRODUTOS
        for($i = 1; $i <= 100; $i++) {
            Product::create([
                "code" => $i * 21,
                "status" =>  "published",
                "imported_t" =>  "2020-02-07T16:00:00Z",
                "url" =>  "https://world.openfoodfacts.org/product/20221126",
                "creator" =>  "securita",
                "created_t" =>  now()->subDays($i),
                "last_modified_t" =>  now()->subDays($i),
                "product_name" =>  "Madalenas quadradas",
                "quantity" =>  "380 g (6 x 2 u.)",
                "brands" =>  "La Cestera",
                "categories" =>  "Lanches comida, Lanches doces, Biscoitos e Bolos, Bolos, Madalenas",
                "labels" =>  "Contem gluten, Contém derivados de ovos, Contém ovos",
                "cities" =>  "",
                "purchase_places" =>  "Braga,Portugal",
                "stores" =>  "Lidl",
                "ingredients_text" =>  "farinha de trigo, açúcar, óleo vegetal de girassol, clara de ovo, ovo, humidificante (sorbitol), levedantes químicos (difosfato dissódico, hidrogenocarbonato de sódio), xarope de glucose-frutose, sal, aroma",
                "traces" =>  "Frutos de casca rija,Leite,Soja,Sementes de sésamo,Produtos à base de sementes de sésamo",
                "serving_size" =>  "madalena 31.7 g",
                "serving_quantity" =>  $i * 31.7,
                "nutriscore_score" =>  $i * 3,
                "nutriscore_grade" =>  "d",
                "main_category" =>  "en:madeleines",
                "image_url" =>  "https://static.openfoodfacts.org/images/products/20221126/front_pt.5.400.jpg"
            ]);
        }
    }

    //FUNÇÃO DE TESTE DE API ONLINE
    public function teste_is_api_online(){
        $response = $this->getJson("/api");
        $response->assertStatus(200);
    }

    //TESTE DE BUSCA DE PRODUTOS
    public function test_can_get_products()
    {
        //COLETAR PRODUTOS
        $products = Product::get()->take(100);        
        $response = $this->getJson("/api/products");
        $response->assertStatus(200);
    }

    //TESTE DE BUSCA DE PRODUTO POR CODIGO
    public function test_can_get_product()
    {
        //COLETAR PRODUTOS
        $product = Product::first();        
        $response = $this->getJson("/api/products/{$product->code}");
        $response->assertStatus(200)->assertJsonPath('code', $product->code);
    }
    
    //TESTE DE ATUALIZAÇÃO DE PRODUTOS
    public function test_can_update_product()
    {
        //COLETAR PRODUTO
        $product = Product::first();
        $data = array_merge($product->toArray(), ['product_name' => 'Updated Name']);
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->apiToken)
                        ->putJson("/api/products/{$product->code}", $data);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'code' => $product->code,
            'product_name' => 'Updated Name'
        ]);
    }
    
    //TESTE DE REMOÇÃO DE PRODUTOS
    public function test_can_delete_product()
    {//COLETAR PRODUTO
        $product = Product::first();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->apiToken)
                        ->deleteJson("/api/products/{$product->code}");
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'code' => $product->code,
            'status' => ProductStatusEnum::TRASH->value
        ]);
    }
    
    //TESTE DE PAGINAÇÃO
    public function test_pagination_works()
    {
        //COLETAR PRODUTO
        $products = Product::get()->take(25);
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