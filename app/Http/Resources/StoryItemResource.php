<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		if($this->type == 2){
			$sType = 'video';
		} else {
			$sType = 'photo';
		}
        return [
            'id' => $this->id,
			'type' => $sType,
            'length' => $this->length,
            'src' => route('single.image', $this->image),
            'preview' => route('single.image', $this->image),
			'link' => $this->link,
			'name' => $this->name,
			'seen' => false,
			'strtime' => strtotime($this->updated_at),
        ];
    }
}
