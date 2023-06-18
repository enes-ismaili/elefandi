<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\Category;

class ProductCategories extends Component
{
    public $selectedParent = 0;
    public $selectedSub = 0;
    public $selectedSubSub = 0;
    public $categories;
    public $subCategories;
    public $subsubCategories;
    public $firstCat = 0;
    public $secondCat = 0;
    public $thirdCat = 0;
    public $oldid= false;

    public function mount($category = [])
    {
        $this->categories = Category::where('parent', '0')->get();
        if($category){
            if($this->oldid){
                $category = Category::find($category);
            }
            if($category->parent){
                $secondLevel = Category::find($category->parent);
                if($secondLevel->parent) {
                    $thirdLevel = Category::find($secondLevel->parent);
                    $this->selectedParent = $thirdLevel->id;
                    $this->selectedSub = $secondLevel->id;
                    $this->selectedSubSub = $category->id;
                    $this->updatedSelectedParent();
                    $this->updatedSelectedSub();
                } else {
                    $this->selectedParent = $secondLevel->id;
                    $this->selectedSub = $category->id;
                    $this->selectedSubSub = 0;
                    $this->updatedSelectedParent();
                    $this->updatedSelectedSub();
                }
            } else {
                $this->selectedParent = $category->id;
                $this->selectedSub = 0;
                $this->selectedSubSub = 0;
                $this->updatedSelectedParent();
            }
        }
    }

    public function render()
    {
        return view('livewire.products.product-categories');
    }

    public function updatedSelectedParent()
    {
        if($this->selectedParent){
            $currCat = Category::find($this->selectedParent);
            $this->subCategories = $currCat->children;
        }
    }

    public function updatedSelectedSub()
    {
        if($this->selectedParent && $this->selectedSub){
            $currCat = Category::find($this->selectedSub);
            $this->subsubCategories = $currCat->children;
        }
    }
}
