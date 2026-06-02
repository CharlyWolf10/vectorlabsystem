<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorPagar extends Model
{
    protected $table = 'cuentas_por_pagar';

    protected $fillable = [
        'proveedor_id', 'compra_id', 'monto_total', 
        'saldo_pendiente', 'fecha_vencimiento', 'estado'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
