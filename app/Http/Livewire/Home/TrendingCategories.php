<?php

namespace App\Http\Livewire\Home;

use Livewire\Component;
use App\Models\Category;

class TrendingCategories extends Component
{
    public $trendingCategories;
    public $selectedCategory;

    public function mount()
    {
        $this->trendingCategories = Category::where('trending', '=', '1')->get();
        $this->selectedCategory = $this->trendingCategories->first();
    }
    
    public function render()
    {
        return view('livewire.home.trending-categories');
    }

    public function changeSelected($cid, $index=0)
    {
        if(is_numeric($cid)){
            $this->selectedCategory = Category::where('id', '=', $cid)->first();
            $this->dispatchBrowserEvent('reinitSlider', $index);
        }
    }
}
