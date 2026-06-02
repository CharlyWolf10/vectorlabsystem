<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellidos',
        'es_estudiante',
        'matricula',
        'email',
        'telefono',
        'acepta_marketing',
        'limite_credito',
        'saldo_pendiente'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
