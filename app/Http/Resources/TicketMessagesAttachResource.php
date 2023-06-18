<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessagesAttachResource extends JsonResource
{
	/**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		$fileUrl = asset('/photos/ticket/'.$this->file);
		$ext = pathinfo($fileUrl, PATHINFO_EXTENSION);
		$lExt = strtolower($ext);
		$type = 2;
		if(in_array($lExt, array('png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp'))){
			$type = 1;
		}
		return [
			'file' => $fileUrl,
			'name' => $this->file,
			'type' => $type,
        ];
	}
}