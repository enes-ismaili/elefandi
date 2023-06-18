<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = ['vendor_id', 'country_id', 'transport', 'limit', 'cost', 'transtime'];

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
