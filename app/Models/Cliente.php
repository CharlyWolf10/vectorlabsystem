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
        'escuela',
        'matricula',
        'email',
        'telefono',
        'acepta_marketing',
        'limite_credito',
        'saldo_pendiente',
        'rfc',
        'constancia_fiscal'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
