<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'rfc',
        'telefono',
        'correo',
        'sitio_web',
        'logo_path',
    ];
}
