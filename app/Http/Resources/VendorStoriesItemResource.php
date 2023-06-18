<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorStoriesItemResource extends JsonResource
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
        if($this->type == 1){
            $image = asset('photos/story/'.$this->image);
        } else {
            $image = 'assets/images/video-210.jpg';
        }
		return [
			'id' => $this->id,
			'name' => $this->name,
			'link' => $this->link,
			'type' => $this->type,
			'length' => $this->length,
			'image' => $image,
            'status' => $statusText,
			'created_at' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
            'views' => (($this->main->fview && $this->main->fview > 1 )?$this->cview * $this->main->fview : $this->cview),
            'clicks' => (($this->main->fclick && $this->main->fclick > 1 )?$this->clicks * $this->main->fclick : $this->clicks),
            'cstatus' => (($this->cactive == 0) ? true : false)
        ];
	}
}