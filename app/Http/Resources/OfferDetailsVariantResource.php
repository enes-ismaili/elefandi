<?php

namespace App\Http\Resources;

use App\Models\OfferDetail;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferDetailsVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        $variant = OfferDetail::where([['offer_id', $request->id],['prod_id', $this->product_id], ['variant_id', $this->id]])->select('id', 'action', 'discount')->first();
        if(!$variant) {
            $variant = [
                'id' => 0,
                'name' => '',
                'discount' => '',
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'variant' => $variant,
        ];
    }


}
