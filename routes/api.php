<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Laravel 11 añade el prefijo 'api' automáticamente a estas rutas
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders/sync', [OrderController::class, 'sync']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::get('/orders/{id}', [OrderController::class, 'show']);