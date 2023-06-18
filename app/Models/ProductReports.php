<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReports extends Model
{
    // public $timestamps = false;

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
