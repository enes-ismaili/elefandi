<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->minoffers() && $this->minoffers()->discount != 0){
            $offer = $this->minoffers();
            if($offer->type < 3){
                $offerPrice = round($this->price - (($this->price * $offer->discount)/100), 2);
                $offerDiscount = '-'.round($offer->discount, 1).'%';
            } else {
                $offerPrice = $offer->discount;
                $offerDiscount = '-'.($this->price - $offer->discount).'€';
            }
            $offerDetail = [
                'offer' => true,
                'cost' => $this->price,
                'nprice' => $offerPrice,
                'discount' => $offerDiscount,
            ];
        } else {
            $offerDetail = [
                'offer' => false,
                'cost' => $this->price,
                'nprice' => 0,
                'discount' => 0,
            ];
        }
        $productImage = asset('/photos/products/'.$this->image);
        $productImageB = asset('/photos/products/230/'.$this->image);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $offerDetail,
            'stock' => $this->stock,
            "vendor_name" => $this->owner->name,
            "vendor_verified" => $this->owner->verified,
            'image' => ((\File::exists('photos/products/230/'.$this->image)) ? $productImageB : ((\File::exists('photos/products/'.$this->image))?$productImage:'assets/images/no-image-210.png')),
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
    }
}
