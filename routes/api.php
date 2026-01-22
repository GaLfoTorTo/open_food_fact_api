<?php

use Illuminate\Support\Facades\Route;
use App\Infrastructure\Http\Controllers\HistoryController;
use App\Infrastructure\Http\Controllers\ProductController;


Route::get('/', [ProductController::class, 'status']);
Route::get('/history', [HistoryController::class, 'index']);
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{code}', [ProductController::class, 'show']);
    Route::put('/{code}', [ProductController::class, 'update']);
    Route::delete('/{code}', [ProductController::class, 'delete']);
});