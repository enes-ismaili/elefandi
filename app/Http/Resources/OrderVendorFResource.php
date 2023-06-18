<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderTrackingResource;

class OrderVendorFResource extends JsonResource
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
        $orderDetails = [];
        foreach($this->details as $detail){
            array_push($orderDetails,new OrderDetailResource($detail));
        }
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'vendor' => $this->vendor->name,
            'transport' => $this->transport * 1,
            'value' => $this->value * 1,
            'total' => bcadd(($this->transport * 1), ($this->value * 1), 4)*1,
            'status' => $orderStatus,
            'sstatus' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('H:i d.m.Y'),
            'products' => $orderDetails,
            'tracks' => OrderTrackingResource::collection($this->tracking),
			'user_info' => [
				'name' => $this->order->user->first_name.' '.$this->order->user->last_name,
				'address' => $this->order->user->address,
				'city' => $this->order->user->zipcode.', '.((is_numeric($this->order->user->city))?$this->order->user->cities->name : $this->order->user->city),
				'country' => $this->order->user->country()->name,
				'phone' => $this->order->user->phone,
				'email' => $this->order->user->email,
			],
			'user_address' => [
				'name' => $this->order->address->name,
				'address' => $this->order->address->address,
				'address2' => $this->order->address->address2,
				'city' => $this->order->address->zipcode.', '.((is_numeric($this->order->address->city))?$this->order->address->cityF->name : $this->order->address->city),
				'country' => $this->order->address->country->name,
				'phone' => $this->order->address->phone,
			],
        ];
    }
}
