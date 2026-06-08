<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'user_id',
        'accion',
        'detalles',
    ];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
