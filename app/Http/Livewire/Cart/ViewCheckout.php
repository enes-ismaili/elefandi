<?php

namespace App\Http\Livewire\Cart;

use App\Models\Coupon;
use Livewire\Component;
use App\Models\Category;

class ViewCheckout extends Component
{
    public $totalPrice = 0;
    public $transPrice = 0;
    public $currentCountry = 1;
    protected $jproducts;
    public $transPriceVend=[];
    public $hasCoupon = false;
    public $coupon;
    public $couponArray = [];

    protected $listeners = [
        'cgetCart' => 'getCartsJs',
        'change-countries' => 'changeCuntry'
    ];

    public function mount()
    {
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $this->currentCountry = $_COOKIE['country_id'];
        }
        if (isset($_COOKIE['couponCode'])) {
            $this->couponCode = $_COOKIE['couponCode'];
            $this->checkCoupon();
        }
    }

    public function render()
    {
        if(current_user()){
            $currentCart = current_user()->cart;
        } else {
            $currentCart = [];
            if($this->jproducts){
                $currentCart = (array) $this->jproducts;
            }
        }
        $this->emit('getCartLoad');

        return view('livewire.cart.view-checkout', [
            'currentCart' => $currentCart
        ]);
    }

    public function getCartsJs($currentCart)
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
                }
            }
        }
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->coupon = '';
        $this->hasCoupon = false;
        $this->couponArray = [];
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

    public function changeCountryReload()
    {
        return redirect()->route('view.cart');
    }
}
