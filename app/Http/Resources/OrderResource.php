<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Http\Resources\OrderVendorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }

    public function toArray($request)
    {
        $orderStatus = 'Duke Procesuar';
        if($this->status == 1){
            $orderStatus = 'Dërguar';
        } else if($this->status == 2){
            $orderStatus = 'Anulluar';
        } else if($this->status == 3){
            $orderStatus = 'Dërguar & Anulluar';
        }
        return [
            'id' => $this->id,
            'transport' => $this->transport * 1,
            'value' => $this->value * 1,
            'total' => bcadd(($this->transport * 1), ($this->value * 1), 4) * 1,
            'notes' => $this->notes,
            'status' => $orderStatus,
            'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
    }

}
