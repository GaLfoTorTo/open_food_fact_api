<?php

namespace App\Infraestructure\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Services\ProductService;
use App\Infraestructure\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    private $productService = ProductService::class;

    public function __construct() 
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'health']);
    }

    /* 
    * FUNÇÃO DE STATUS DE API
    */
    public function status()
    {
        return response()->json([
            'status' => 'online',
            'database' => $this->checkDatabase(),
            'last_cron' => $this->checkLastCron(),
            'uptime' => $this->checkUptime(),
            'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
        ]);
    }
    /**
     * FUNÇÃO DE LISTAGEM DE PRODUTOS
     */
    public function index()
    {
        $perPage = $request->get('per_page', 20);
        $query = Product::where('status', '!=', ProductStatus::TRASH);
        $products = $query->paginate($perPage);
        return response()->json($products);
    }

    /**
     * FUNÇÃO DE VISUALIZAÇÃOO DE PRODUTO
     */
    public function show(string $code)
    {
        $product = Product::where('code', $code)->where('status', '!=', ProductStatus::TRASH)->firstOrFail();
        return response()->json($product);
    }

    /**
     * FUNÇÃO DE ATUALIZAÇÃO DE PRODUTO
     */
    public function update(ProductRequest $request, string $code)
    {
        $product = Product::where('code', $code)->firstOrFail();
        
        $validated = $request->validate();
        $this->productService->createOrUpdate($validated);
        
        return response()->json($product);
    }

    /**
     * FUNÇÃO DE REMOÇÃO DE PRODUTO
     */
    public function delete(string $code)
    {
        $product = Product::where('code', $code)->firstOrFail();
        $product->update(['status' => ProductStatus::TRASH]);
        return response()->json(['message' => 'Produto removido com sucesso!']);
    }

    /* 
    * FUNÇÃO VERIFICAÇÃO DE BANCO DE DADOS
    */
    private function checkDatabase(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'Conectado';
        } catch (\Exception $e) {
            return 'Desconectado';
        }
    }

    /* 
    * FUNÇÃO DE BUSCA DE ULTIMA EXECUÇÃO DO CRON
    */
    private function checkLastCron()
    {
        $history = \App\Domain\Hisotry\Models\History::latest()->first();
        return $history ? $history->completed_at : 'Nunca executado';
    }

    /* 
    * FUNÇÃO DE BUSCA DE TEMPO ONLINE
     */
    private function checkUptime(): string
    {
        // Implementar lógica para obter tempo online
        return '24 hours'; // Exemplo
    }

    
}
