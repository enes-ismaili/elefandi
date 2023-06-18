<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
