<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArqueoCaja extends Model
{
    protected $table = 'arqueos_caja';

    protected $fillable = [
        'user_id', 'fondo_inicial', 
        'monedas_50c', 'monedas_1', 'monedas_2', 'monedas_5', 'monedas_10', 'monedas_20',
        'billetes_50', 'billetes_100', 'billetes_200', 'billetes_500',
        'total_calculado', 'total_registrado_sistema', 'diferencia'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
