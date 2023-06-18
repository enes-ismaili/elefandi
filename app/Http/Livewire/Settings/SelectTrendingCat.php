<?php

namespace App\Http\Livewire\Settings;

use App\Models\Tag;
use Livewire\Component;
use App\Models\Category;

class SelectTrendingCat extends Component
{
    public $pid;
    public $category;
    public $selectedTags = [];
    public $selectedTagsId = [];
    public $tagS;
    public $tags;

    protected $queryString = [
        'tagS' => ['except' => '']
    ];

    public function mount()
    {
        if($this->pid){
            $this->category = Category::where('id', $this->pid)->first();
            $this->selectedTags = $this->category->trendingtag;
            ray($this->selectedTags);
        }
    }

    public function render()
    {
        if(strlen($this->tagS) >= 3){
            $this->tags = Tag::where('name', 'like', '%'.$this->tagS.'%')->take(7)->get();
        } else {
            $this->tags = [];
        }
        return view('livewire.settings.select-trending-cat', ['category']);
    }

    public function updateTagOrder($reorder)
    {
        if(count($reorder) > 1){
            $selectedTags = $this->selectedTags;
            $newSelectedTags = [];
            foreach($reorder as $order){
                $current = collect($selectedTags)->where('id', $order['value'])->first();
                $newSelectedTags[] = $current;
            }
            $this->selectedTags = $newSelectedTags;
        }
    }

    public function addTag($tagId)
    {
        if(is_numeric($tagId)){
            if(!in_array($tagId, $this->selectedTagsId)){
                $this->selectedTagsId[] = $tagId;
                $this->selectedTags[] = Tag::where('id', '=', $tagId)->first();
                $this->tagS = '';
            }
        }
    }

    public function removeTag($id)
    {
        if(is_numeric($id)) {
            $selectedTags = $this->selectedTags;
            // unset $this->selectedTagsId[$id];
            $collection = collect($selectedTags);
            $collectionIds = collect($this->selectedTagsId);
            $key = $collection->where('id', $id)->keys()->first();
            $current = collect($selectedTags)->forget($key);
            $this->selectedTags = $current;
            unset($collectionIds[$key]);
            $this->selectedTagsId = $collectionIds;
        }
    }
}
