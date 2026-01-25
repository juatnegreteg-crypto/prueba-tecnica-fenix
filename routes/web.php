<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('orders');
});

Route::get('/check-db', function () {
    $orders = \App\Models\Order::all();

    return view('check-db', [
        'total' => $orders->count(),
        'orders' => $orders,
        'db_path' => database_path('database.sqlite'),
    ]);
});
;

