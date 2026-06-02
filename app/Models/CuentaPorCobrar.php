<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';
    
    protected $fillable = [
        'cliente_id', 'monto_total', 'saldo_pendiente', 'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
