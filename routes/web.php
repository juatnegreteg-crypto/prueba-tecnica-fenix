<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('orders');
});

Route::get('/check-db', function () {
    return response()->json([
        'total_registros' => \App\Models\Order::count(),
        'datos' => \App\Models\Order::all(),
        'ubicacion_db' => database_path('database.sqlite'),
    ], 200, [], JSON_PRETTY_PRINT);
});

