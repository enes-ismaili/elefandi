<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $couponDisscount = '-'.($this->discount).'%';
        if($this->type == 1){
            $couponType = 'Kupon mbi Dyqanin';
        } else if($this->type == 2){
            $couponType = 'Kupon mbi Kategoritë';
        } else if($this->type == 3){
            $couponType = 'Kupon mbi Produktet';
        }
        $startDate = Carbon::parse($this->start_date);
        $now = Carbon::now();
        $endDate = Carbon::parse($this->expire_date);
        $couponStatus = 2;
        if($startDate->lt($now)){
            $couponStatus = 0;
            if($endDate->gt($now)){
                $couponStatus = 1;
            }
        }
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'typeT' => $couponType,
            'discount' => $couponDisscount,
            'status' => $couponStatus,
            'start' => Carbon::parse($this->start_date)->format('d.m.Y H:i'),
            'expire' => Carbon::parse($this->expire_date)->format('d.m.Y H:i'),
        ];
    }

}
