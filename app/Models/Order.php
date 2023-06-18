<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function ordervendor()
    {
        return $this->hasMany(OrderVendor::class, 'order_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
    
    public function detailsId($pid)
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->where('product_id', '=', $pid);
    }

    public function childrenRecursive() {
        return $this->details()->with('childrenRecursive');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class, 'id', 'address_id');
    }
}
