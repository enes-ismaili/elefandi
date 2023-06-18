<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OpenHourResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'monday' => $this->monday,
            'monday_start' => Carbon::parse($this->monday_start)->format('H:i'),
            'monday_end' => Carbon::parse($this->monday_end)->format('H:i'),
            'tuesday' => $this->tuesday,
            'tuesday_start' => Carbon::parse($this->tuesday_start)->format('H:i'),
            'tuesday_end' => Carbon::parse($this->tuesday_end)->format('H:i'),
            'wednesday' => $this->wednesday,
            'wednesday_start' => Carbon::parse($this->wednesday_start)->format('H:i'),
            'wednesday_end' => Carbon::parse($this->wednesday_end)->format('H:i'),
            'thursday' => $this->thursday,
            'thursday_start' => Carbon::parse($this->thursday_start)->format('H:i'),
            'thursday_end' => Carbon::parse($this->thursday_end)->format('H:i'),
            'friday' => $this->friday,
            'friday_start' => Carbon::parse($this->friday_start)->format('H:i'),
            'friday_end' => Carbon::parse($this->friday_end)->format('H:i'),
            'saturday' => $this->saturday,
            'saturday_start' => Carbon::parse($this->saturday_start)->format('H:i'),
            'saturday_end' => Carbon::parse($this->saturday_end)->format('H:i'),
            'sunday' => $this->sunday,
            'sunday_start' => Carbon::parse($this->sunday_start)->format('H:i'),
            'sunday_end' => Carbon::parse($this->sunday_end)->format('H:i'),
        ];
    }
}
