<?php

namespace App\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Domain\History\Models\History;
use App\Domain\History\Enums\HistoryStatusEnum;
use App\Domain\History\Services\HistoryService;

class HistoryController extends Controller
{
    /**
     * FUNÇÃO DE LISTAGEM DE PRODUTOS
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $histories = History::paginate($perPage);
            return response()->json($histories, 200);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                "message" => "Nehnum histórico de cron encontrado"
            ], 404);
        }
    }    
}
