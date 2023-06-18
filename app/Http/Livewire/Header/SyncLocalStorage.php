<?php

namespace App\Http\Livewire\Header;

use Livewire\Component;
use App\Models\WishList;
use App\Models\ShoppingCart;

class SyncLocalStorage extends Component
{
    public $cartLocal, $wishLocal;
    public $syncWish = true;
    protected $listeners = ['updateLocal' => 'syncLocalStorages'];

    public function render()
    {
        return view('livewire.header.sync-local-storage');
    }

    public function syncLocalStorages($cartIn, $wishlistIn)
    {
        if($cartIn){
            $carts = json_decode($cartIn);
            if($carts){
                foreach($carts as $cart){
                    if(current_user()) {
                        $cardQty = 1;
                        if($cart->qty){
                            $cardQty = $cart->qty;
                        }
                        ShoppingCart::updateOrCreate(
                            ['product_id' => $cart->id, 'variant_id' => $cart->variant_id, 'user_id'=> current_user()->id],
                            ['qty' => $cardQty]
                        );
                    }
                }
            }
        }
        if($wishlistIn && $this->syncWish){
            $wishlists = json_decode($wishlistIn);
            if($wishlists){
                foreach($wishlists as $wishlist){
                    if(current_user()) {
                        WishList::updateOrCreate(
                            ['product_id' => $wishlist->id, 'variant_id' => 0, 'user_id'=> current_user()->id],
                        );
                    }
                }
            }
        }
        $this->emitTo('products.product-price', 'updateCart');
    }
}
