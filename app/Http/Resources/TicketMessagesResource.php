<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TicketMessagesAttachResource;

class TicketMessagesResource extends JsonResource
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
			'id' => $this->id,
			'way' => $this->way,
			'user' => ($this->way == 1) ? (($this->user) ? $this->user->first_name.' '.$this->user->last_name : '') : (($this->vendor)?$this->vendor->name:'Stafi Elefandi'),
			'message' => $this->message,
			'attachment' => TicketMessagesAttachResource::collection($this->attachment),
			'created' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
	}
}