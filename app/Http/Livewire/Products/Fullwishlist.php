<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;

class Fullwishlist extends Component
{
    public $wishlistss = [];
    public $isLoggedIn = false;
    public $getFromJson = false;
    protected $jproducts;

    protected $listeners = [
        'getWishlistsFulls' => 'getWishFJs',
        'getFWishLUpdate'
    ];

    public function mount()
    {
        if(current_user()){
            $this->isLoggedIn = true;
            $this->wishlistss = current_user()->wishlist;
        }
    }

    public function render()
    {
        if(!$this->isLoggedIn){
            if(!$this->getFromJson){
                $this->emit('getWishFu', "true");
            }
            if($this->jproducts){
                $this->wishlistss = $this->jproducts;
                $this->getFromJson = true;
            }
        }
        return view('livewire.products.fullwishlist');
    }

    public function removeWishlist($wid)
    {
        current_user()->wishlist()->where('id', '=', $wid)->delete();
        $this->wishlistss = current_user()->wishlist;
        $this->emitTo('header.wishlist', 'getWishLUpdate');
    }

    public function getWishFJs($currentWish)
    {
        $this->jproducts = (array) json_decode($currentWish);
        $this->wishlistss = $this->jproducts;
        $this->prodCount = count($this->jproducts);
        $this->getFromJson = true;
    }

    public function getFWishLUpdate()
    {
        $this->wishlistss = current_user()->wishlist;
    }
}
