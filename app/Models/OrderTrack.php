<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTrack extends Model
{
    public function orderVendor()
    {
        return $this->hasOne(OrderVendor::class, 'id', 'order_vendor_id');
    }
}
