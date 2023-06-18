<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OfferDetailsVariantResource;

class OfferDetailsProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected static $using = [];

    public static function using($using = [])
    {
        static::$using = $using;
    }


    public function toArray($request)
    {
        $allVariants = OfferDetailsVariantResource::collection($this->product->variants()->select('id', 'name', 'price', 'product_id')->get());
        return [
            'id' => $this->id,
            'type' => $this->type,
            'prod_id' => $this->prod_id,
            'product' => $this->product()->select('id', 'name', 'price')->first(),
            'variant_id' => $this->variant_id,
            'allVariants' => $allVariants,
            // 'allVariants' => $this->product->variants()->select('id', 'name', 'price', 'product_id')->get(),
            'allVariantsCount' => $allVariants->count(),
            // 'variant' => $this->variant()->select('id', 'name', 'price')->get(),
            // 'variant1' => $this->main->details()->where('prod_id', $this->prod_id)->get(),
            'action' => $this->action,
            'discount' => $this->discount,
            'active' => $this->active,
        ];
    }

}
