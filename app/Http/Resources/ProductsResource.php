<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
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
            'price' => ($this->price * 1),
            'stock' => $this->stock,
            'image' => asset('/photos/products/'.$this->image),
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
    }
}
