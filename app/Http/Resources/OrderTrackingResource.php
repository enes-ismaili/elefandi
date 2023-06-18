<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Http\Resources\OrderDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
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
            'comment' => $this->comment,
            'created_at' => Carbon::parse($this->created_at)->format('H:i d.m.Y'),
        ];
    }
}
