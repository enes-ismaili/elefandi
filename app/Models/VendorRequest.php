<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRequest extends Model
{
    public function owners()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class, 'city');
    }
}
