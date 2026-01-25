<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('orders');
});

Route::get('/check-db', function () {
    // Esto obtiene todas las órdenes y las muestra en formato bonito
    return response()->json([
        'total_registros' => \App\Models\Order::count(),
        'datos' => \App\Models\Order::all(),
        'ubicacion_db' => database_path('database.sqlite'),
    ]);
});
