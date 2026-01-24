<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

protected $fillable = [
    'bitfinex_id', 
    'symbol', 
    'type', 
    'amount', 
    'price', 
    'status', 
    'mts_create'
];
}
