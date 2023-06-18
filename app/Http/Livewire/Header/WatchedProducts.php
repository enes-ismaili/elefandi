<?php

namespace App\Http\Livewire\Header;

use Livewire\Component;
use App\Models\Country;

class WatchedProducts extends Component
{
    public $products;
    public $openWatched = false;
    protected $listeners = [
        'getWatched',
    ];

    public function mount()
    {
        # code...
    }

    public function render()
    {
        return view('livewire.header.watched-products');
    }

    public function openW()
    {
        if($this->openWatched){
            $this->openWatched = false;
        } else {
            $this->openWatched = true;
        }
        $this->dispatchBrowserEvent('reInitWatchedSwiper');
    }

    public function closeW()
    {
        $this->openWatched = false;
    }

    public function getWatched($products)
    {
        if($products){
            $allProducts = JSON_decode($products);
            $this->products = array_reverse($allProducts);
        }
    }
}