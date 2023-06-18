<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorNameRequest extends Model
{
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }
}
