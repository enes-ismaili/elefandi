<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
			'name' => $this->name,
			'address' => $this->address,
			'phone' => $this->phone,
			'zipcode' => $this->zipcode,
			'city' => (is_numeric($this->city))?$this->cityF->name:$this->city,
			'country_id' => $this->country->name,
			'primary' => $this->primary,
        ];
	}
}