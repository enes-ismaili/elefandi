<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }

    public function toArray($request)
    {
        $offerCount = 0;
        if($this->action == 1){
            $offerCount = '-'.($this->discount).'%';
        } else if($this->action == 2){
            $offerCount = '-'.($this->discount).'€';
        }
        if($this->type == 1){
            $offerType = 'Oferë për Dyqanin';
        } else if($this->type == 2){
            $offerType = 'Oferë për Kategorinë';
        } else if($this->type == 3){
            $offerType = 'Oferë për disa Produkte';
            $offerCount = 'Ulje për secilin';
        }
        $startDate = Carbon::parse($this->start_date);
        $startDate = Carbon::parse($this->start_date);
        $now = Carbon::now();
        $endDate = Carbon::parse($this->expire_date);
        $offerStatus = 2;
        if($startDate->lt($now)){
            $offerStatus = 0;
            if($endDate->gt($now)){
                $offerStatus = 1;
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'typeT' => $offerType,
            'discount' => $offerCount,
            'status' => $offerStatus,
            'start' => Carbon::parse($this->start_date)->format('d.m.Y H:i'),
            'expire' => Carbon::parse($this->expire_date)->format('d.m.Y H:i'),
        ];
    }

}
