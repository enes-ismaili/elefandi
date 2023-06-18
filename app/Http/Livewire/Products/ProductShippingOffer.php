<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use App\Models\Country;

class ProductShippingOffer extends Component
{
    public $vendor;
    public $hasShippingS = false;
    public $countryName;
    public $shippingLimit;
    protected $listeners = [
        'updateCountry',
    ];

    public function mount()
    {
        $this->hasShippingS = false;
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $currentCountry = $_COOKIE['country_id'];
        } else {
            $currentCountry = 1;
        }
        if($this->vendor){
            $vendorShipping = $this->vendor->shippings()->where('country_id', '=', $currentCountry)->first();
            if($vendorShipping){
                if($vendorShipping->transport == 2 || $vendorShipping->transport == 4){
                    if($vendorShipping->limit > 0){
                        $this->hasShippingS = true;
                        $this->countryName = Country::findOrFail($currentCountry);
                        $this->shippingLimit = $vendorShipping->limit;
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.products.product-shipping-offer');
    }

    public function updateCountry($id)
    {
        $this->hasShippingS = false;
        if($id){
            if($this->vendor){
                $vendorShipping = $this->vendor->shippings()->where('country_id', '=', $id)->first();
                if($vendorShipping){
                    if($vendorShipping->transport == 2 || $vendorShipping->transport == 4){
                        if($vendorShipping->limit > 0){
                            $this->hasShippingS = true;
                            $this->countryName = Country::findOrFail($id);
                            $this->shippingLimit = $vendorShipping->limit;
                        }
                    }
                }
            }
        }
    }
}