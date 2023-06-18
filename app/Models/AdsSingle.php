<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsSingle extends Model
{
    public function ads()
    {
        return $this->hasOne(Ads::class, 'id', 'ads_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }
}
