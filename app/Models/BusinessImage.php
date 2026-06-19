<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessImage extends Model
{
    //
    protected $fillable = ['business_profile_id', 'path', 'description'];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }
}
