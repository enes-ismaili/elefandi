<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SVendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $cityAddress = ($this->city && is_numeric($this->city)) ? (($this->cities)?$this->cities->name:$this->city) : $this->city;
        $countryAddress = (is_numeric($this->country_id) && $this->country()) ? $this->country()->name : $this->country_id;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country_id' => $this->country_id,
            'address_city' => $cityAddress.', '.$countryAddress,
            'phone' => $this->phone,
            'email' => $this->email,
            'verified' => $this->verified,
            'logo_path' => ((\File::exists('photos/vendor/'.$this->logo_path)) ? asset('photos/vendor/'.$this->logo_path) : 'assets/images/no-image-210.png'),
            'cover_path' => ((\File::exists('photos/cover/'.$this->cover_path)) ? asset('photos/cover/'.$this->cover_path) : 'assets/images/vendor-cover.jpg'),
        ];
    }
}