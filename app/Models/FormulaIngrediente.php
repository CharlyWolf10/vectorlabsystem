<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaIngrediente extends Model
{
    protected $guarded = [];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
