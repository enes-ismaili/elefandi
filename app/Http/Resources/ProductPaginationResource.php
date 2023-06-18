<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPaginationResource extends JsonResource
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
            'current_page' => $this->currentPage(),
            'data' => ProductResource::collection($this->items()),
            // 'data' => $this->items(),
            'last_page' => $this->lastPage(),
            'total' => $this->total(),
        ];
    }
}
