<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\Category;

class AddCategory extends Component
{
    public $name, $slug, $description, $image, $icon, $parent;
    public $categories = [];

    public function mount()
    {
        $this->name = '';
        $this->categories = Category::all();
    }
    
    public function render()
    {
        return view('livewire.products.add-category');
    }
}
