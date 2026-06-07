<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $guarded = [];

    public function ingredientes()
    {
        return $this->hasMany(FormulaIngrediente::class);
    }
}
