<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // if($this->minoffers() && $this->minoffers()->discount != 0){
        //     $offer = $this->minoffers();
        //     if($offer->type < 3){
        //         $offerPrice = round($this->price - (($this->price * $offer->discount)/100), 2);
        //         $offerDiscount = '-'.round($offer->discount, 1).'%';
        //     } else {
        //         $offerPrice = $offer->discount;
        //         $offerDiscount = '-'.($this->price - $offer->discount).'€';
        //     }
        //     $offerDetail = [
        //         'offer' => true,
        //         'cost' => $this->price,
        //         'nprice' => $offerPrice,
        //         'discount' => $offerDiscount,
        //     ];
        // } else {
        //     $offerDetail = [
        //         'offer' => false,
        //         'cost' => $this->price,
        //         'nprice' => 0,
        //         'discount' => 0,
        //     ];
        // }
        $vPrice = $this->price;
        if($vPrice == 0){
            $vPrice = $this->product->price;
        }
        $vStock = $this->stock;
        if($vStock == 0){
            $vStock = $this->product->stock;
        }
        if($this->product->offers($this->id) && $this->product->offers($this->id)->discount != 0) {
            $offer = $this->product->offers($this->id);
            if($offer->type < 3){
                $offerPrice = round($vPrice - (($vPrice * $offer->discount)/100), 2);
                $offerDiscount = '-'.round($offer->discount, 1).'%';
            } else {
                $offerPrice = $offer->discount;
                $offerDiscount = ($vPrice - $offer->discount).'€';
            }
            if($offerPrice == $vPrice){
                $offerDetail = [
                    'offer' => false,
                    'cost' => $vPrice,
                    'nprice' => 0,
                    'discount' => 0,
                    'expire' => false,
                ];
            } else {
                $offerExpire = \Carbon\Carbon::parse($offer->main->expire_date)->format('U');
                $offerDetail = [
                    'offer' => true,
                    'cost' => $vPrice,
                    'nprice' => $offerPrice,
                    'discount' => $offerDiscount,
                    'expire' => $offerExpire.'000',
                ];
            }
        } else {
            $offerDetail = [
                'offer' => false,
                'cost' => $vPrice,
                'nprice' => 0,
                'discount' => 0,
                'expire' => false,
            ];
        }
        // $offerDetail = [
        //     'offer' => false,
        //     'cost' => $this->price,
        //     'nprice' => 0,
        //     'discount' => 0,
        // ];
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $offerDetail,
            'slug' => $this->slug,
            'stock' => $vStock,
            'sku' => $this->sku,
            'image' => (($this->image) ? asset('/photos/products/'.$this->image) : ''),
        ];
    }
}
