<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    public function ads()
    {
        return $this->hasMany(AdsSingle::class, 'ads_id', 'id');
    }
}
