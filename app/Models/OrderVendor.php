<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderVendor extends Model
{
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_vendor_id');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function tracking()
    {
        return $this->hasMany(OrderTrack::class, 'order_vendor_id');
    }
}
