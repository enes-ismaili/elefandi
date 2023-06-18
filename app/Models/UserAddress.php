<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function cityF()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }
}
