<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorStoriesResource extends JsonResource
{
	/**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $statusText = 'në Pritje';
        if($this->cactive == 1){
            $statusText = 'Aprovuar';
        } elseif($this->cactive == 2){
            $statusText = 'Rishikim';
        } elseif($this->cactive == 3){
            $statusText = 'Refuzuar';
        }
        if($this->items()->count()){
            if($this->items()->where('type',1)->first()){
                $image = asset('photos/story/'.$this->items()->where('type',1)->first()->image);
            } else {
                $image = 'assets/images/video-210.jpg';
            }
        } else {
            $image = 'assets/images/no-image-210.png';
        }
		return [
			'id' => $this->id,
			'name' => $this->name,
			'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
			'image' => $image,
            'status' => $statusText.(($this->needaction == 1) ? ', Story në Pritje' : ''),
            'cstatus' => (($this->needaction == 1 || $this->cactive == 0) ? true : false)
        ];
	}
}