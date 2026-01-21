<?php

namespace App\Domain\Products\Services;

use App\Domain\Products\Models\Product;
use App\Domain\Products\Models\ImportHistory;
use App\Domain\Products\Enums\ProductStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductService
{
    private $baseUrl  = 'https://challenges.coode.sh/food/data/json/';

    //FUNÇÃO DE IMPORTAÇÃO DE PRODUTOS
    public function importProducts(): array
    {   
        //INICIALIZAR HISTORICO DE IMPORTACAO
        $history = ImportHistory::create([
            'started_at' => now(),
            'status' => 'running'
        ]);

        try {
            //BUSCAR ARQUIVOS DE IMPORTAÇÃO
            $files = $this->getFileList();
            $total = 0;
            
            //IMPORTAR ARQUIVOS
            foreach ($files as $file) {
                $imported = $this->importFromFile($file);
                $total += $imported;
                
                if ($total >= 100) {
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
                'error_message' => $e->getMessage()
            ]);
            //ALERTA DE FALHA
            Log::emergency('Falha na importação: ' . $e->getMessage());
            throw $e;
        }
    }

    //FUNÇÃO DE CRIAÇÃO OU ATUALIZAÇÃO DE PRODUTO
    public function createOrUpdate(array $product): array
    {
        $productData = array_merge($data, [
            'imported_t' => now(),
            'status' => $data['status'] ?? ProductStatus::DRAFT,
        ]);
        
        Product::updateOrCreate(
            ['code' => $data['code']],
            $productData
        );
    }
    //FUNÇÃO DE BUSCA DE ARQUIVOS DE IMPORTAÇÃO
    private function getFileList(): array
    {
        $resp = Http::get($this->baseUrl . 'index.txt');
        return array_slice(explode("\n", $resp->body()), 0, 10);
    }

    //FUNÇÃO DE REGISTRO DE PRODUTO IMPORTADO
    private function importFile(string $file):int
    {
        //BUSCAR PRODUTO
        $resp = Http::get($this->baseUrl . $filename);
        $content = gzdecode($response->body());
        $lines = explode("\n", $content);
        //CONTADOR DE PRODUTOS IMPORTADOS
        $imported = 0;

        foreach (array_slice($lines, 0, 50) as $line) {
            if (empty($line)) continue;
            //DECODIFICAR PRODUTO    
            $data = json_decode($line, true);
            if ($data) {
                $dataForm = $this->convertTimestamps($data);
                $this->createOrUpdate($dataForm);
                $imported++;
            }
        }
        
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