<?php

namespace App\Http\Resources;

use App\Http\Resources\StoryItemResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $nowTime = date('Y-m-d H:i:s');
        $storyImage = $this->items()->where('type',1)->first();
        if($storyImage){
            $storyImage = asset('/photos/story/'.$storyImage->image);
        } else {
            $storyImage = '';
        }
        return [
            'id' => 'ST'.$this->id,
            'photo' => $storyImage,
            'name' => $this->name,
			'link' => '',
			'lastUpdated' => strtotime($this->updated_at),
			'items' => StoryItemResource::collection($this->items->where('cactive', '=', '1')->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime)),
        ];
    }
}
