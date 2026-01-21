<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Product\Services\ProductService;

class ProductDailyImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importação diara de produtos';

    /**
     * Execute the console command.
     */
    public function handle(ProductService $importService)
    {
        $this->info('Starting product import...');
        
        try {
            $result = $importService->importProducts();
            $this->info("Importação bem sucedida! {$result['count']} produtos importadoss.");
        } catch (\Exception $e) {
            $this->error("Importação falhou: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}