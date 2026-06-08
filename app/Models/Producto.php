<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nombre',
        'codigo',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'proveedor_id',
        'categoria',
        'ingreso_tipo_default',
        'ingreso_paquetes_default',
        'ingreso_unidades_default',
        'aplica_iva'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
