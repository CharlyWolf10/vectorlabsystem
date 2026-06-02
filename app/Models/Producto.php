<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo'
    ];
}
