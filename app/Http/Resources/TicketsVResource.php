<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsVResource extends JsonResource
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
			'message' => $this->message,
			'vendor' => $this->user->first_name.' '.$this->user->last_name,
			'created' => Carbon::parse($this->created_at)->format('d.m.Y H:i'),
        ];
	}
}