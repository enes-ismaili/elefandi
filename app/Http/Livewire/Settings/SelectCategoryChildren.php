<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;
use App\Models\Category;

class SelectCategoryChildren extends Component
{
    public $pid;
    public $category;
    public $selectedCategory = [];
    public $selectedCategoryId = [];
    public $catS;
    public $categories;
    public $categoriesIn;

    protected $queryString = [
        'catS' => ['except' => '']
    ];

    public function mount()
    {
        if($this->pid){
            $this->category = Category::where('id', $this->pid)->first();
            $categoriesIn = [];
            foreach($this->category->children as $child){
                $categoriesIn[] = $child->id;
                $categoriesIn = array_merge($categoriesIn, $child->children->pluck('id')->toArray());
            }
            $this->categoriesIn = $categoriesIn;
            $this->selectedCategory = $this->category->categoryChildHome;
        }
    }

    public function render()
    {
        if(strlen($this->catS) >= 3){
            $categoryIn = $this->categoriesIn;
            $this->categories = Category::where('name', 'like', '%'.$this->catS.'%')->whereIn('id', $categoryIn)->take(7)->get();
        } else {
            $this->categories = [];
        }
        return view('livewire.settings.select-category-children', ['category']);
    }

    public function updateChildOrder($reorder)
    {
        if(count($reorder) > 1){
            $selectedCategory = $this->selectedCategory;
            $newSelectedTags = [];
            foreach($reorder as $order){
                $current = collect($selectedCategory)->where('id', $order['value'])->first();
                $newSelectedTags[] = $current;
            }
            $this->selectedCategory = $newSelectedTags;
        }
    }

    public function addCategory($catId)
    {
        if(is_numeric($catId)){
            if(!in_array($catId, $this->selectedCategoryId)){
                $this->selectedCategoryId[] = $catId;
                $this->selectedCategory[] = Category::where('id', '=', $catId)->first();
                $this->catS = '';
            }
        }
    }

    public function removeCat($id)
    {
        if(is_numeric($id)) {
            $selectedCategory = $this->selectedCategory;
            // unset $this->selectedTagsId[$id];
            $collection = collect($selectedCategory);
            $collectionIds = collect($this->selectedCategoryId);
            $key = $collection->where('id', $id)->keys()->first();
            $current = collect($selectedCategory)->forget($key);
            $this->selectedCategory = $current;
            unset($collectionIds[$key]);
            $this->selectedCategoryId = $collectionIds;
        }
    }
}
