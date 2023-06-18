<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TicketMessagesResource;
use App\Http\Resources\TicketMessagesAttachResource;

class TicketSingleResource extends JsonResource
{
	/**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		$ticketStatus = 'Në Pritje';
		if($this->status == 1){
			$ticketStatus = 'Përgjigjur';
		} else if($this->status == 2){
			$ticketStatus = 'Kërkesë për Mbyllje';
		} else if($this->status == 3){
			$ticketStatus = 'Mbyllur nga Dyqani';
		} else if($this->status == 4){
			$ticketStatus = 'Rishikim nga Elefandi';
		} else if($this->status == 6){
            $ticketStatus = 'Mbyllur Përfundimisht';
        } else if($this->status == 7){
            $ticketStatus = 'Rikthim Pagese';
        }
		if($this->type == 1){
			$ticketType = 'Porosia nuk ka mbërritur';
		} else if($this->type == 2){
			$ticketType = 'Probleme me Produktin';
		} else if($this->type == 3){
			$ticketType = 'Kërkesë për Rimbursim';
		} else {
			$ticketType = $this->subject;
		}
		return [
			'id' => $this->id,
			'subject' => $ticketType,
			'status' => $ticketStatus,
			'sstatus' => $this->status,
			'message' => $this->message,
			'messages' => TicketMessagesResource::collection($this->messages),
			'vendor' => $this->vendor->name,
			'attachment' => TicketMessagesAttachResource::collection($this->attachment),
			'created' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
	}
}