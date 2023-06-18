<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\WishList;
use App\Models\ShoppingCart;

class AddToCart extends Component
{
    public $product;
    public $isLoggedIn;
    public $plink;
    public $hasVariants = false;
    public $addCartText = 'Shto në Shportë';
    public $wishList = false;
    public $inCart = false;
    public $isInCart = false;
    public $isInWish = false;
    
    public function mount()
    {
        if($this->isLoggedIn){
            if(current_user()->cart->where('product_id', '=', $this->product->id)->count()){
                $this->addCartText = 'Është në Shportë';
                $this->isInCart = true;
            }
            if(current_user()->wishlist->where('product_id', '=', $this->product->id)->count()){
                $this->isInWish = true;
            }
        }
        if($this->product->colors || $this->product->attributes){
            $this->hasVariants = true;
            $this->addCartText = 'Zgjidh Variantin';
        }
    }

    public function render()
    {
        return view('livewire.products.add-to-cart');
    }

    public function addToCart()
    {
        $variantId = 0;
        if($this->isInCart){
            $isCart = ShoppingCart::where('product_id', '=', $this->product->id)->where('variant_id', '=', $variantId)->where('user_id', '=', current_user()->id)->delete();
            $this->isInCart = false;
            $this->addCartText = 'Shto në Shportë';
        } else {
            if(current_user()) {
                ShoppingCart::updateOrCreate(
                    ['product_id' => $this->product->id, 'variant_id' => $variantId, 'user_id'=> current_user()->id],
                    ['qty' => 1]
                );
                $this->isInCart = true;
                $this->addCartText = 'Është në Shportë';
            }
        }
        $this->emitTo('header.mini-cart', '$refresh');
    }

    public function addToWish(){
        $variantId = 0;
        if($this->isInWish){
            $this->product->wishlist()->where('product_id', '=', $this->product->id)->where('user_id', '=', current_user()->id)->delete();
            $this->isInWish = false;
        } else {
            if(current_user()) {
                WishList::updateOrCreate(
                    ['product_id' => $this->product->id, 'variant_id' => $variantId, 'user_id'=> current_user()->id],
                );
                $this->inWish = true;
            }
            $this->isInWish = true;
        }
        $this->emitTo('header.wishlist', 'getWishLUpdate');
    }
}
