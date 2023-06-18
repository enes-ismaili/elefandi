<?php

namespace App\Http\Livewire\Products;

use App\Models\City;
use App\Models\User;
use App\Models\Order;
use App\Mail\NewOrder;
use App\Models\Country;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderDetail;
use App\Models\OrderVendor;
use App\Models\UserAddress;
use App\Models\ProductVariant;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class BuyDirectly extends Component
{
    public $product;
    public $stock;
    public $variant;
    public $variantName;
    public $price;
    public $showCheckout = false;
    public $currentCountry;
    public $shippingCost;
    public $isLoggedIn = false;
    public $user;
    public $fname, $lname, $email, $phone, $address, $zipcode, $shippingaddress;
    public $additionalinformation = '';
    public $countries;
    public $cities;
    public $country;
    public $city;
    public $userAddress;
    public $continueCheckout = true;
    public $isCorrect = true;

    protected $listeners = [
        'openCheckout',
        'addressAdded'
    ];
    
    public function mount()
    {
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $this->currentCountry = $_COOKIE['country_id'];
        } else {
            $this->currentCountry = 1;
        }

        if(current_user()){
            $this->isLoggedIn = true;
            $user = current_user();
            $this->user = $user;
            $this->fname = $user->first_name;
            $this->lname = $user->last_name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->address = $user->address;
            $this->zipcode = $user->zipcode;
            $this->country = $user->country_id;
            $this->city = $user->city;
            $this->userAddress = current_user()->addresses->where('country_id', '=', $this->currentCountry)->where('udelete', '=', 0);
            if(!current_user()->first_name || !current_user()->phone || !current_user()->email || !current_user()->country_id || !current_user()->city || !current_user()->address){
                $this->isCorrect = false;
            }
        } else {
            $this->country = $this->currentCountry;
            $this->userAddress = [];
        }

        $getShipping = $this->product->shippings->where('country_id', '=', $this->currentCountry)->first();
        $shippingCTransport = false;
        if(!$this->product->shipping && $getShipping){
            if($getShipping->free){
                $this->shippingCost = 0;
            } else {
                $this->shippingCost = $getShipping->cost;
            }
            if($getShipping->shipping == 1){
                $shippingCTransport = true;
            }
        } else {
            $getShipping = $this->product->owner->shippings()->where('country_id', '=', $this->currentCountry)->first();
            if($getShipping->cost){
                $this->shippingCost = $getShipping->cost;
            } else {
                $this->shippingCost = 0;
            }
            if($getShipping->transport > 0){
                $shippingCTransport = true;
            }
        }

        $this->countries = Country::where('shipping', '=', 1)->get();
        if($this->country){
            $this->cities = City::where('country_id', $this->country)->get();
        }
    }

    public function render()
    {
        return view('livewire.products.buy-directly');
    }

    public function openCheckout($stock, $variant, $vname, $price)
    {
        $this->stock = $stock;
        if($stock && $stock >= 0){
            $this->continueCheckout = true;
        } else {
            $this->continueCheckout = false;
        }
        $this->variant = $variant;
        $this->variantName = $vname;

        $this->price = $price;
        $this->showCheckout = true;
    }
    
    public function closeModal()
    {
        $this->showCheckout = false;
    }

    public function updatedCountry()
    {
        if(!$this->isLoggedIn){
            if($this->country){
                $this->currentCountry = $this->country;
                $this->cities = City::where('country_id', $this->country)->get();
                $this->city = '';
                $getShipping = $this->product->shippings->where('country_id', '=', $this->currentCountry)->first();
                if($getShipping){
                    $this->shippingCost = $getShipping->cost;
                } else {
                    $getShipping = $this->product->owner->shippings()->where('country_id', '=', $this->currentCountry)->first();
                    if($getShipping){
                        // $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                        // $productShippings .= '"c'.$country->id.'":{"id":"'.$getShippingVendor->id.'","shipping":"'.$getShippingVendor->limit.'","free":"0","cost":"'.$getShippingVendor->cost.'","shipping_time":"'.$getShippingVendor->transtime.'", "timeName":"'.$transportInfo->name.'"}';
                    }
                    $this->shippingCost = $getShipping->cost;
                }
            } else {
                $this->city = '';
                $this->cities = '';
            }
        }
    }

    public function addressAdded()
    {
        if($this->isLoggedIn){
            $this->userAddress = current_user()->addresses->where('country_id', '=', $this->currentCountry)->where('udelete', '=', 0);
        }
    }

    public function saveCheckout()
    {
        if(current_user()){
            
        } else {
            $validatedDate = $this->validate([
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
                'country' => 'required|min:1',
                'city' => 'required|min:1',
            ], [
                'fname.required' => 'Emri është i detyruar',
                'lname.required' => 'Mbiemri është i detyruar',
                'email.required' => 'Email-i është i detyruar',
                'email.email' => 'Email-i nuk është i rregullt',
                'email.unique' => 'Email është regjistruar më parë. Provoni të hyni ose shkoni tek kam harruar fjalkalimin nëse nuk ju kujtohen te dhënat',
                'phone.required' => 'Numri i Telefonit është i detyruar',
                'address.required' => 'Adresa është i detyruar',
                'country.required' => 'Shteti është i detyruar',
                'country.min' => 'Shteti është i detyruar',
                'city.required' => 'Qyteti është i detyruar',
                'city.min' => 'Qyteti është i detyruar',
            ]);
        }
        if($this->shippingaddress || !$this->isLoggedIn){
            $ordersProducts = [];
            $allProducts = [];
            $prodTransV = [];
            $prodCostV = [];
            $totalPrice = 0;
            $transTotalPrice = 0;
    
            $currProduct = $this->product;
            $prodId = $this->product->id;
            $currentVariant = $currProduct->cartVariant($this->variant);
            $noStock = false;
            $stockFrom = 1;
            if($currentVariant){
                $prodPrice = $currentVariant->price ? $currentVariant->price : $currProduct->price;
                if($currProduct->offers($this->variant)){
                    $offer = $currProduct->offers($this->variant);
                    if($offer->type < 3){
                        $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                    } elseif($offer->discount == 0) {
                        $prodPrice = $prodPrice;
                    } else {
                        $prodPrice = $offer->discount;
                    }
                } else {
                    $offer = '';
                }
                if($currentVariant->stock){
                    $prodMaxStock = $currentVariant->stock;
                    $stockFrom = 2;
                } else {
                    $prodMaxStock = $currProduct->stock;
                }
                // $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $currProduct->stock;
                $productStock = $this->stock;
                if($prodMaxStock){
                    if($productStock > $prodMaxStock){
                        $productStock = $prodMaxStock;
                    }
                } else {
                    $noStock = true;
                }
            } else {
                $prodPrice = $currProduct->price;
                if($currProduct->offers()){
                    $offer = $currProduct->offers();
                    if($offer->type < 3){
                        $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                    } else {
                        $prodPrice = $offer->discount;
                    }
                } else {
                    $offer = '';
                }
                $prodMaxStock = $currProduct->stock;
                $productStock = $this->stock;
                if($prodMaxStock){
                    if($productStock > $prodMaxStock){
                        $productStock = $prodMaxStock;
                    }
                } else {
                    $productStock = 0;
                    $prodMaxStock = 0;
                    $noStock = true;
                }
            }
            $getShipping = $currProduct->shippings->where('country_id', '=', $this->currentCountry)->first();
            if($getShipping){
                $shippingCost = $getShipping->cost;
            } else {
                $getShipping = $currProduct->owner->shippings()->where('country_id', '=', $this->currentCountry)->first();
                if($getShipping){
                    // $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                    // $productShippings .= '"c'.$country->id.'":{"id":"'.$getShippingVendor->id.'","shipping":"'.$getShippingVendor->limit.'","free":"0","cost":"'.$getShippingVendor->cost.'","shipping_time":"'.$getShippingVendor->transtime.'", "timeName":"'.$transportInfo->name.'"}';
                }
                $shippingCost = $getShipping->cost;
            }
            if(!$noStock && (($getShipping && $getShipping->shipping == 1))){
                // ray($prodPrice);
                $ordersProducts[$prodId]['variant_id'] = $this->variant;
                $ordersProducts[$prodId]['qty'] = $productStock;
                $ordersProducts[$prodId]['price'] = $prodPrice;
                $ordersProducts[$prodId]['personalize'] = ''; //Personalize
    
                $totalPrice += ($productStock * $prodPrice);
                // if(isset($transPriceVend[$currProduct->owner->id]) && $transPriceVend[$currProduct->owner->id]){
                //     $transPriceVend[$currProduct->owner->id]['cost'] += $shippingCost;
                // } else {
                //     $transPriceVend[$currProduct->owner->id]['cost'] = $shippingCost;
                //     $transPriceVend[$currProduct->owner->id]['name'] = $currProduct->owner->name;
                // }
                $transTotalPrice += $shippingCost;
    
                $allProducts[$currProduct->owner->id][] = $currProduct;
                if(isset($prodTransV[$currProduct->owner->id]) && $prodTransV[$currProduct->owner->id]){
                    $prodTransV[$currProduct->owner->id] += $shippingCost;
                } else {
                    $prodTransV[$currProduct->owner->id] = ($shippingCost *1);
                }
                if(isset($prodCostV[$currProduct->owner->id]) && $prodCostV[$currProduct->owner->id]){
                    $prodCostV[$currProduct->owner->id] += ($productStock * $prodPrice);
                } else {
                    $prodCostV[$currProduct->owner->id] = ($productStock * $prodPrice);
                }
            }
            $order = new Order();
            if(current_user()){
                $order->user_id = current_user()->id;
                $order->address_id = $this->shippingaddress; //Adresa LogIn
                $user = current_user();
            } else {
                $user = new User();
                $user->first_name = $this->fname;
                $user->last_name = $this->lname;
                $user->email = $this->email;
                $user->password = Hash::make('password'); // TODO: generate pass
                $user->phone = $this->phone;
                $user->address = $this->address;
                $user->zipcode = $this->zipcode;
                $user->city = $this->city;
                $user->country_id = $this->country;
                $user->save();
                $userId = $user->id;
                $order->user_id = $userId;
    
                $address = new UserAddress();
                $address->user_id = $userId;
                $address->name = $this->fname.' '.$this->lname;
                $address->phone = $this->phone;
                $address->address = $this->address;
                $address->address2 = ''; // Address2
                $address->zipcode = $this->zipcode;
                $address->city = $this->city;
                $address->country_id = $this->country;
                $address->save();
                $addressId = $address->id;
                $order->address_id = $addressId;
            }
            $order->notes = $this->additionalinformation;
            $order->value = $totalPrice;
            $order->transport = $transTotalPrice;
            $order->save();
            foreach($allProducts as $key=>$prodVendor){
                $vendOrder = new OrderVendor();
                $vendOrder->order_id = $order->id;
                $vendOrder->vendor_id = $key;
                $vendOrder->value = $prodCostV[$key];
                $vendOrder->transport = $prodTransV[$key];
                $vendOrder->save();
                foreach($prodVendor as $product){
                    $orderDetail = new OrderDetail();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->order_vendor_id = $vendOrder->id;
                    $orderDetail->product_id = $product->id;
                    $orderDetail->variant_id = $ordersProducts[$product->id]['variant_id'];
                    $orderDetail->price = $ordersProducts[$product->id]['price'];
                    $orderDetail->qty = $ordersProducts[$product->id]['qty'];
                    $orderDetail->personalize = $ordersProducts[$product->id]['personalize'];
                    $orderDetail->save();
                    
                    if($stockFrom == 2){
                        if($currentVariant && $currentVariant->stock){
                            $thisVariant = ProductVariant::find($this->variant);
                            $newStock = $currentVariant->stock - $productStock;
                            $thisVariant->stock = $newStock;
                            $thisVariant->save();
                        }
                    } else {
                        $newStock = $prodMaxStock - $productStock;
                        $currProduct->stock = $newStock;
                    }
                    // $currentProduct = Product::find($product->id);
                    // $newStock = $product->stock - 1;
                    // $currentProduct->stock = $newStock;
                    // $currentProduct->save();
                    $newSales = $currProduct->sales + $productStock;
                    $currProduct->sales = $newSales;
                    $currProduct->save();
                    // current_user()->cart()->where([['product_id', '=', $product->id],['variant_id', '=', $ordersProducts[$product->id]['variant_id']]])->delete();
                }
                // Mail::to($vendOrder->vendor->email)->send(new NewOrder($user, $order, $vendOrder));
            }
            // Mail::to(current_user()->email)->send(new NewOrder($user, $order, false));
            if(current_user()){
                session()->put('success', 'ordersuccess','Porosia u krye me sukses. Ju do të njoftoheni me email ose mund të shihni në profilin tuaj përditsimet në lidhje me porosinë.');
                return redirect()->route('profile.orders.index');
            } else {
                session()->put('success','Porosia u krye me sukses. Ju do të njoftoheni me email ose mund të shihni në profilin tuaj përditsimet në lidhje me porosinë');
                return redirect()->route('home');
            }
        } else {
            $this->addError('shippingaddress', 'Zgjidhni ose shtoni një adresë se ku do të dërgohet ky produkt.');
        }
    }
}
