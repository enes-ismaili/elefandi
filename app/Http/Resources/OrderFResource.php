<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Http\Resources\OrderVendorFResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderFResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $orderStatus = 'Duke Procesuar';
        if($this->status == 1){
            $orderStatus = 'Dërguar';
        } else if($this->status == 2){
            $orderStatus = 'Anulluar';
        } else if($this->status == 3){
            $orderStatus = 'Dërguar & Anulluar';
        }
        $orderVendors = [];
        foreach($this->ordervendor as $vendor){
            array_push($orderVendors,new OrderVendorFResource($vendor));
        }
        return [
            'id' => $this->id,
            'transport' => $this->transport * 1,
            'value' => $this->value * 1,
            'total' => bcadd(($this->transport * 1), ($this->value * 1), 4) * 1,
            'notes' => $this->notes,
            'status' => $orderStatus,
            'created_at' => Carbon::parse($this->created_at)->format('H:i d.m.Y'),
            'order_vendor' => $orderVendors,
			'user_info' => [
				'name' => $this->user->first_name.' '.$this->user->last_name,
				'address' => $this->user->address,
				'city' => $this->user->zipcode.', '.((is_numeric($this->user->city))?$this->user->cities->name : $this->user->city),
				'country' => $this->user->country()->name,
				'phone' => $this->user->phone,
				'email' => $this->user->email,
			],
			'user_address' => [
				'name' => $this->address->name,
				'address' => $this->address->address,
				'address2' => $this->address->address2,
				'city' => $this->address->zipcode.', '.((is_numeric($this->address->city))?$this->address->cityF->name : $this->address->city),
				'country' => $this->address->country->name,
				'phone' => $this->address->phone,
			],
        ];
    }
}
