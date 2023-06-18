<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $product = $this->product;
        if($product->minoffers() && $product->minoffers()->discount != 0){
            $offer = $product->minoffers();
            if($offer->type < 3){
                $offerPrice = round($product->price - (($product->price * $offer->discount)/100), 2);
                $offerDiscount = '-'.round($offer->discount, 1).'%';
            } else {
                $offerPrice = $offer->discount;
                $offerDiscount = '-'.($product->price - $offer->discount).'€';
            }
            $offerDetail = [
                'offer' => true,
                'cost' => $product->price,
                'nprice' => $offerPrice,
                'discount' => $offerDiscount,
            ];
        } else {
            $offerDetail = [
                'offer' => false,
                'cost' => $product->price,
                'nprice' => 0,
                'discount' => 0,
            ];
        }
        $productImage = asset('/photos/products/'.$product->image);
        $productImageB = asset('/photos/products/230/'.$product->image);
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $offerDetail,
            'stock' => $product->stock,
            "vendor_name" => $product->owner->name,
            "vendor_verified" => $product->owner->verified,
            'image' => ((\File::exists('photos/products/230/'.$product->image)) ? $productImageB : ((\File::exists('photos/products/'.$product->image))?$productImage:'assets/images/no-image-210.png')),
            'created_at' => Carbon::parse($product->created_at)->format('d.m.Y H:i'),
        ];
    }

}
