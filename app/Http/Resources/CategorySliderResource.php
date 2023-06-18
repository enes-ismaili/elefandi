<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategorySliderResource extends JsonResource
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
            'button' => $this->button,
            'image' => asset('/photos/category/'.$this->image),
            'link' => '',
            // 'link' => $this->link,
        ];
    }
}
