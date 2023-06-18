<?php

namespace App\Http\Livewire\Header;

use App\Models\Country;
use Livewire\Component;
use App\Models\ShoppingCart;

class MiniCart extends Component
{
    protected $listeners = [
        'getCarts' => 'getCartJs',
        '$refresh'
    ];
    public $showCart = false;
    protected $jproducts;
    public $getFromJson = false;
    public $prodCount = 0;
    public $isLogged = false;
    public $shippingCountry;
    public $currentCountry;

    public function mount()
    {
        if(current_user()){
            $this->isLogged = true;
        }
        // TODO: cache shippingCountry
        $this->shippingCountry = Country::where('shipping', '1')->get();
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $this->currentCountry = $_COOKIE['country_id'];
        } else {
            $this->currentCountry = 1;
        }
    }
    
    public function render()
    {
        if(current_user()){
            //$currentCart = current_user()->cart;
            $currentCart = current_user()->cart()->whereHas('product', function ($query) {
                $query->where('status', '=', '1')->where('vstatus', '=', 1);
            })->get();
            $this->prodCount = count($currentCart);
        } else {
            $currentCart = [];
            if($this->jproducts){
                $currentCart = (array) $this->jproducts;
                $this->getFromJson = true;
                $this->prodCount = count($currentCart);
            }
        }
        return view('livewire.header.mini-cart', [
            'products' => $currentCart
        ]);
    }

    public function updatedCurrentCountry(){
        setcookie('country_id', $this->currentCountry, time() + (30*3110400), "/");
        $this->emit('changeCountry', $this->currentCountry);
    }

    public function removeProduct($ids)
    {
        ShoppingCart::where('id', $ids)->first()->delete();
        $this->emitTo('products.product-price', 'removeCarts');
    }

    public function showCart($action)
    {
        if($action == 1){
            $this->showCart = true;
            $this->emit('getCart', "Updated Salary.");
        } else {
            $this->showCart = false;
        }
    }

    public function getCartJs($currentCart)
    {
        $this->jproducts = json_decode($currentCart);
    }
}
