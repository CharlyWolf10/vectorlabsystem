<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = [
        'proveedor_id', 'user_id', 'concepto', 'monto', 
        'metodo_pago', 'fecha', 'comprobante_path'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cuentaPorPagar()
    {
        return $this->hasOne(CuentaPorPagar::class);
    }
}
