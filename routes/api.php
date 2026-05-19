<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\Api\InventoryController;

// Define your API routes here

Route::post('/mpesa/stkpush', [MpesaController::class, 'stkPush']);
Route::post('/mpesa/callback', [MpesaController::class, 'callback']);

// Inventory API routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventory/product/{product}/stock', [InventoryController::class, 'getProductStock']);
    Route::post('/inventory/products/stock', [InventoryController::class, 'getMultipleProductStock']);
    Route::get('/inventory/low-stock', [InventoryController::class, 'getLowStockProducts']);
});
