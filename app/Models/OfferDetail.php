<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferDetail extends Model
{
    public $timestamps = false;

    protected $fillable = ['offer_id', 'vendor_id', 'type', 'prod_id', 'variant_id', 'action', 'discount', 'active'];

    public function main()
    {
        return $this->hasOne(Offer::class, 'id', 'offer_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'prod_id');
    }

    public function variant()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'variant_id');
    }
}
