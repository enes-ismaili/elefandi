<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
			'country' => $this->country->name,
			'country_id' => $this->country_id,
            'shipping' => $this->shipping,
            'cost' => $this->cost,
            'free' => ($this->free) ? '1' : '0',
            'shipping_time' => $this->shipping_time,
        ];
    }
}
