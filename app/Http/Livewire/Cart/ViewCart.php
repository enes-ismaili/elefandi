<?php

namespace App\Http\Livewire\Cart;

use App\Models\Coupon;
use App\Models\Country;
use Livewire\Component;
use App\Models\Category;
use App\Models\ShoppingCart;

class ViewCart extends Component
{
    protected $listeners = [
        'sgetCart' => 'getCartssJs',
        'change-countries' => 'changeCuntry'
    ];

    public $showCart = false;
    protected $jproducts;
    public $getFromJson = false;
    public $prodCount = 0;
    public $isLogged = false;
    public $shippingCountry;
    public $currentCountry;
    public $couponCode;
    public $coupon;
    public $hasCoupon = false;
    public $couponText = '';
    public $couponArray = [];
    public $showCouponMsg = false;

    public function mount()
    {
        $this->showCart = true;
        if(current_user()){
            $this->isLogged = true;
        }
        $this->shippingCountry = Country::where('shipping', '1')->get();
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $this->currentCountry = $_COOKIE['country_id'];
        } else {
            $this->currentCountry = 1;
        }

        if (isset($_COOKIE['couponCode'])) {
            $this->couponCode = $_COOKIE['couponCode'];
            $this->checkCoupon();
        }
    }

    public function updatedShowCart()
    {
        $this->emitSelf('getCartLoad', "Updated Salary.");
    }

    public function updatedCurrentCountry(){
        setcookie('country_id', $this->currentCountry, time() + (30*3110400), "/");
        $this->emit('changeCountry', $this->currentCountry);
    }

    public function render()
    {
        if(current_user()){
            $currentCart = current_user()->cart;
            $this->prodCount = count($currentCart);
        } else {
            $currentCart = [];
            if(!$this->jproducts){
                $this->emit('getCart', "Updated Salary.");
            }
            if($this->jproducts){
                $currentCart = (array) $this->jproducts;
                $this->getFromJson = true;
                $this->prodCount = count($currentCart);
            }
        }
        $this->emit('getCartLoad', 'true');
        return view('livewire.cart.view-cart', [
            'products' => $currentCart
        ]);
    }

    public function addCartQty($shopid, $max)
    {
        $thisProdCart = ShoppingCart::where('id', $shopid)->first();
        if($thisProdCart){
            $currentQty = $thisProdCart->qty +1;
            if($currentQty <= $max){
                $thisProdCart->qty = $currentQty;
                $thisProdCart->save();
            }
        }
    }

    public function removeCartQty($shopid, $max)
    {
        $thisProdCart = ShoppingCart::where('id', $shopid)->first();
        if($thisProdCart){
            $currentQty = $thisProdCart->qty - 1;
            if($currentQty >= 1){
                $thisProdCart->qty = $currentQty;
                $thisProdCart->save();
            }
        }
    }

    public function removeProduct($ids)
    {
        ShoppingCart::where('id', $ids)->first()->delete();
    }

    public function getCartssJs($currentCart)
    {
        $this->jproducts = json_decode($currentCart);
    }

    public function checkCoupon()
    {
        if($this->couponCode) {
            $couponCode = $this->couponCode;
            $coupon = Coupon::where('code', '=', $couponCode)->first();
            if($coupon){
                $today = time();
                $startDate = strtotime($coupon->start_date);
                $expireDate = strtotime($coupon->expire_date);
                if($today > $startDate && $today < $expireDate){
                    return $this->addCoupon();
                }
            }
            return $this->removeCoupon();
        }
    }

    public function addCoupon()
    {
        $this->showCouponMsg = false;
        if($this->couponCode) {
            $couponCode = $this->couponCode;
            $coupon = Coupon::where('code', '=', $couponCode)->first();
            if($coupon){
                $today = time();
                $startDate = strtotime($coupon->start_date);
                $expireDate = strtotime($coupon->expire_date);
                if($today > $startDate && $today < $expireDate){
                    setcookie('couponCode', $couponCode, $expireDate, "/");
                    $this->coupon = $coupon;
                    $this->hasCoupon = true;
                    $couponAction = ($coupon->action == 1) ? '%' : '€';
                    if($coupon->type == 1){
                        $couponType = ' në dyqanin '.$coupon->vendor->name;
                    } elseif($coupon->type == 2){
                        $categoryList = '';
                        $i = 0;
                        foreach(json_decode($coupon->categories) as $category){
                            $i++;
                            $this->couponArray[] = $category;
                            $thisCategory = Category::where('id', '=', $category)->first();
                            if($i > 1){
                                $categoryList .= ', ';
                            }
                            $categoryList .= $thisCategory->name;
                        }
                        $categoryText = (count($this->couponArray) > 1) ? 'kategoritë' : 'kategorinë';
                        $couponType = ' në '.$categoryText.' '.$categoryList.' të dyqanit '.$coupon->vendor->name;
                    } else {
                        $i = 0;
                        foreach(json_decode($coupon->products) as $product){
                            $i++;
                            $this->couponArray[] = $product;
                        }
                        $couponType = ' për produkte të zgjedhura';
                    }
                    $this->couponText = 'Ju përfitoni '.$coupon->discount.$couponAction.' ulje '.$couponType;
                } else {
                    $this->couponText = 'Kuponi nuk është aktiv';
                    $this->showCouponMsg = true;
                }
            } else {
                $this->couponText = 'Kuponi nuk ekziston';
                $this->showCouponMsg = true;
            }
        }
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->coupon = '';
        $this->hasCoupon = false;
        $this->couponArray = [];
        $this->couponText = '';
        $this->showCouponMsg = false;
        if (isset($_COOKIE['couponCode'])) {
            unset($_COOKIE['couponCode']); 
            setcookie('couponCode', null, -1, '/'); 
        }
    }

    public function changeCuntry()
    {
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $this->currentCountry = $_COOKIE['country_id'];
        } else {
            $this->currentCountry = 1;
        }
    }
}
