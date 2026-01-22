<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\History\Models\History;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Enums\ProductStatusEnum;
use App\Domain\Product\Services\ProductService;
use App\Infrastructure\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
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
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $products = Product::where('status', '!=', ProductStatusEnum::TRASH)->paginate($perPage);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Nehnum produto encontrado"
            ], 404);
        }
    }

    /**
     * FUNÇÃO DE VISUALIZAÇÃOO DE PRODUTO
     */
    public function show(string $code)
    {
        try {
            $product = Product::where('code', $code)->where('status', '!=', ProductStatusEnum::TRASH)->firstOrFail();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Produto nao encontrado",
            ], 404);
        }
    }

    /**
     * FUNÇÃO DE ATUALIZAÇÃO DE PRODUTO
     */
    public function update(ProductRequest $request, string $code)
    {
        //INICIAR TRANSACTION COM DB
        DB::beginTransaction();
        try {
            $product = $this->productService->createOrUpdate($request->validated());
            DB::commit();
            return response()->json($product, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Houve um erro ao atualizar o produto. Tente novamente!',
            ], 500);
        }
    }

    /**
     * FUNÇÃO DE REMOÇÃO DE PRODUTO
     */
    public function delete(string $code)
    {
        //INICIAR TRANSACTION COM DB
        DB::beginTransaction();
        try {
            $product = Product::where('code', $code)->firstOrFail();
            $product->update(['status' => ProductStatusEnum::TRASH]);
            DB::commit();
            return response()->json([
                'message' => 'Produto deletado com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Houve um erro ao deletar o produto. Tente novamente!',
            ], 500);
        }
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
        $history = History::latest()->first();
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
