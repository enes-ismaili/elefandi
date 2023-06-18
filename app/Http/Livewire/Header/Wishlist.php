<?php

namespace App\Http\Livewire\Header;

use Livewire\Component;

class Wishlist extends Component
{
    public $showWish = false;
    public $isLoggedIn = false;
    public $wishlists = [];
    public $wishlistsC = 0;
    protected $jproducts;
    public $getFromJson = false;

    protected $listeners = [
        'getWishlists' => 'getWishJs',
        'getWishlistsUpdate' => 'getWishUpdate',
        'getWishLUpdate',
        '$refresh'
    ];

    public function mount()
    {
        if(current_user()){
            $this->isLoggedIn = true;
            $this->wishlists = current_user()->wishlist->take(6);
            $this->wishlistsC = current_user()->wishlist->count();
        }
    }

    public function render()
    {
        if(!$this->isLoggedIn){
            if($this->jproducts){
                $this->wishlists = array_slice($this->jproducts, 0, 6, true);
                $this->getFromJson = true;
                $this->wishlistsC = count($this->jproducts);
            }
        }
        return view('livewire.header.wishlist');
    }

    public function showWishlist()
    {
        $this->showWish = true;
        $this->emit('getWish', "true");
    }

    public function hideWishlist()
    {
        $this->showWish = false;
    }

    public function removeWishlist($wid)
    {
        current_user()->wishlist()->where('id', '=', $wid)->delete();
        $this->wishlists = current_user()->wishlist->take(6);
        $this->wishlistsC = current_user()->wishlist->count();
        $this->emitTo('products.fullwishlist', 'getFWishLUpdate');
    }

    public function getWishJs($currentWish)
    {
        $this->jproducts = (array) json_decode($currentWish);
        $this->wishlists = array_slice($this->jproducts, 0, 6, true);
        $this->wishlistsC = count($this->jproducts);
        $this->getFromJson = true;
    }

    public function getWishUpdate($currentWish)
    {
        $this->jproducts = (array) json_decode($currentWish);
        $this->wishlistsC = count($this->jproducts);
    }

    public function getWishLUpdate()
    {
        $this->wishlists = current_user()->wishlist->take(6);
        $this->wishlistsC = current_user()->wishlist->count();
    }
}
