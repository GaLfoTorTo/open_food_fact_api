<?php

namespace App\Domain\Product\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Domain\Product\Enums\ProductStatusEnum;
use App\Domain\Product\Models\Product;
use App\Domain\History\Models\History;

class ProductService
{
    private $baseUrl  = 'https://challenges.coode.sh/food/data/json/';
    private $limit = 100;
    private $httpService;

    public function __construct()
    {
        $this->httpService = Http::withOptions([
                'verify' => false,
                'timeout' => 60,
                'connect_timeout' => 30,
            ]);
    }

    //FUNÇÃO DE IMPORTAÇÃO DE PRODUTOS
    public function importProducts(): array
    {   
        //INICIALIZAR HISTORICO DE IMPORTACAO
        $history = History::create([
            'started_at' => now(),
            'status' => 'running'
        ]);

        try {
            //BUSCAR ARQUIVOS DE IMPORTAÇÃO
            $files = $this->getFileList();
            $total = 0;
            
            //IMPORTAR ARQUIVOS
            foreach ($files as $file) {
                $imported = $this->importFile($file);
                $total += $imported;
                
                if ($total >= $this->limit) {
                    break;
                }
            }

            //FINALIZAR HISTORICO DE IMPORTACAO
            $history->update([
                'completed_at' => now(),
                'status' => 'completed',
                'total' => $total
            ]);

            return ['success' => true, 'count' => $total];

         } catch (\Exception $e) {
            //FINALIZAR HISTORICO DE IMPORTACAO
            $history->update([
                'completed_at' => now(),
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
            //ALERTA DE FALHA
            Log::emergency('Falha na importação: ' . $e->getMessage());
            throw $e;
        }
    }

    //FUNÇÃO DE CRIAÇÃO OU ATUALIZAÇÃO DE PRODUTO
    public function createOrUpdate(array $data): array
    {
        $productData = array_merge($data, [
            'imported_t' => now(),
            'status' => $data['status'] ?? ProductStatusEnum::DRAFT,
        ]);

        $product = Product::where('code', $data['code'])->first();

        if (!empty($product)) {
            $productData['last_modified_t'] = now();
            $product->update($productData);
        } else {
            $product = Product::create($productData);
        }
        
        return $product->toArray();
    }

    //FUNÇÃO DE BUSCA DE ARQUIVOS DE IMPORTAÇÃO
    private function getFileList(): array
    {
        $resp = $this->httpService->get($this->baseUrl . 'index.txt');
        return array_slice(explode("\n", $resp->body()), 0, 10);
    }

    //FUNÇÃO DE REGISTRO DE PRODUTO IMPORTADO
    private function importFile(string $file):int
    {
        //CAMINHO PARA ARQUIVO BAIXADO
        $tempPath = storage_path('app/tmp_' . basename($file));

        //BAIXAR ARQUIVO
        file_put_contents(
            $tempPath,
            $this->httpService->get($this->baseUrl . $file)->body()
        );
        
        $imported = 0;
        $handle = gzopen($tempPath, 'rb');
        //VERIFICAR DE ABERTURA DE ARQUIVO
        if (!$handle) {
            throw new \RuntimeException("Não foi possível abrir o arquivo de importação: {$file}");
        }
        //LEITURA DE ARQUIVO
        while (!gzeof($handle) && $imported < $this->limit) {
            $line = gzgets($handle);

            if (!$line) continue;

            $data = json_decode($line, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $this->convertTimestamps($data);
                $this->createOrUpdate($data);
                $imported++;
            }
        }

        //FECHAR E LIMPAR ARQUIVO BAIXADO
        gzclose($handle);
        unlink($tempPath); 
        
        return $imported;
    }

    //FUNÇÃO DE CONVERSÃO DE DATA (UNIX PARA TIMESTAMP)
    private function convertTimestamps(array $data): array
    {
        $timestamps = ['created_t', 'last_modified_t'];
        
        foreach ($timestamps as $field) {
            if (isset($data[$field]) && is_numeric($data[$field])) {
                $data[$field] = Carbon::createFromTimestamp($data[$field]);
            }
        }
        
        return $data;
    }
}