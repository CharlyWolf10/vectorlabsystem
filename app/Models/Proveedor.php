<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'email', 'telefono', 'rfc', 'direccion', 
        'banco', 'titular_cuenta', 'clabe', 'num_cuenta'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function cuentasPorPagar()
    {
        return $this->hasMany(CuentaPorPagar::class);
    }
}
