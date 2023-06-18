<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorRole extends Model
{
    public function vendor()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }
}
