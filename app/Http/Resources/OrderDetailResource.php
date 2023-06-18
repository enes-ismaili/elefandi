<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ProductVariant;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $variantName = ProductVariant::find($this->variant_id);
        return [
            'id' => $this->id,
            'name' => $this->products->name,
            'variant' => $this->variant_id,
            'variant_name' => (($variantName)?$variantName->name:''),
            'price' => $this->price * 1,
            'qty' => $this->qty * 1,
            'personalize' => $this->personalize,
        ];
    }
}
