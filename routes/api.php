<?php

use App\Infrastructure\Http\Controllers\ProductController;

Route::prefix('api')->group(function () {
    Route::get('/', [ProductController::class, 'apiDetail']);
    
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{code}', [ProductController::class, 'show']);
        Route::put('/{code}', [ProductController::class, 'update'])->middleware('auth:sanctum');
        Route::delete('/{code}', [ProductController::class, 'delete'])->middleware('auth:sanctum');
    });
});