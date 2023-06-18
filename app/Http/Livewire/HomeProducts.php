<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class HomeProducts extends Component
{
    public $cproduct;
    public $pageNumber = 1;
    public $hasMorePages = true;
    public $productsCount = 15;

    protected $listeners = [
        'loadMore' => 'loadProducts',
    ];

    public function mount()
    {
        $this->products = Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->orderBy('updated_at', 'DESC')->take(15)->get();
        // $this->products[] = new Collection();
        // $this->loadProducts();
    }

    public function render()
    {
        return view('livewire.home-products');
    }

    public function loadProducts()
    {
        if($this->hasMorePages){
            $this->pageNumber += 1;
            $loadPosts = $this->pageNumber * 15;
            $this->products = Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->orderBy('updated_at', 'DESC')->take($loadPosts)->get();
            if(($this->products)->count() < (($this->productsCount * 1) + 15)){
                $this->hasMorePages = false;
            }
            $this->productsCount = ($this->products)->count();
        }
    }
}
