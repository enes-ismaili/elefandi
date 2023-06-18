<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Order;
use App\Mail\NewOrder;
use App\Models\Product;
use App\Models\OrderTrack;
use App\Models\OrderDetail;
use App\Models\OrderVendor;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserRegisterOrder;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function viewcart()
    {
        return view('user.cart.cart');
    }

    public function checkout()
    {
        if(current_user()){
            $currentCart = current_user()->cart;
            $isLoggedIn = true;
            if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
                $currentCountry = $_COOKIE['country_id'];
            } else {
                $currentCountry = 1;
            }
            $address = current_user()->addresses->where('country_id', '=', $currentCountry)->where('udelete', '=', 0);
        } else {
            $currentCart = [];
            $isLoggedIn = false;
            $address = [];
        }
        return view('user.cart.checkout', compact('currentCart', 'isLoggedIn', 'address'));
    }

    public function checkoutpost(Request $request)
    {
        if(current_user()){
            $validatedDate = $validatedDate = Validator::make($request->all(), [
                'addresses' => 'required',
            ], [
                'addresses.required' => 'Zgjidhni ose shtoni një adresë se ku do të dërgohet ky produkt',
            ]);
        } else {
            $validatedDate = Validator::make($request->all(), [
                'fname' => 'required',
                'lname' => 'required',
                'phone' => 'required',
                'email' => 'required|email|unique:users',
                'country' => 'required',
                'city' => 'required',
                'address' => 'required',
                'sname' => 'required',
                'sphone' => 'required',
                'scity' => 'required',
                'saddress' => 'required',
            ], [
                'fname.required' => 'Emri është i detyrueshëm',
                'lname.required' => 'Mbiemri është i detyrueshëm',
                'phone.required' => 'Numri i telefonit është i detyrueshëm',
                'email.required' => 'Email është i detyrueshëm',
                'email.email' => 'Email është i detyrueshëm',
                'email.unique' => 'Email është regjistruar më parë. Provoni të hyni ose shkoni tek kam harruar fjalkalimin nëse nuk ju kujtohen te dhënat',
                'country.required' => 'Shteti është i detyrueshëm',
                'city.required' => 'Qyteti është i detyrueshëm',
                'address.required' => 'Adresa është i detyrueshëm',
                'sname.required' => 'Personi kontaktues ku do dërgohet është i detyrueshëm',
                'sphone.required' => 'Numri i telefonit ku do dërgohet është i detyrueshëm',
                'scity.required' => 'Qyeti ku do tërgohet është i detyrueshëm',
                'saddress.required' => 'Adresa ku do dërgohet është i detyrueshëm',
            ]);
        }
        if ($validatedDate->fails()) {
            return redirect()->route('view.checkout')->withErrors($validatedDate)->withInput();
        }
        if(current_user()){
            $products = current_user()->cart;
        } else {
            $products = json_decode($request->cartcheckout);
        }
        $ordersProducts = [];
        $allProducts = [];
        $prodTransV = [];
        $prodCostV = [];
        $totalPrice = 0;
        $transTotalPrice = 0;
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $currentCountry = $_COOKIE['country_id'];
        } else {
            $currentCountry = 1;
        }
        $hasCoupon = false;
        $couponDisscountPrice = 0;
        if(isset($_COOKIE['couponCode'])) {
            $couponCode = $_COOKIE['couponCode'];
            if($couponCode) {
                $coupon = Coupon::where('code', '=', $couponCode)->first();
                if($coupon){
                    $today = time();
                    $startDate = strtotime($coupon->start_date);
                    $expireDate = strtotime($coupon->expire_date);
                    if($today > $startDate && $today < $expireDate){
                        $hasCoupon = true;
                        if($coupon->type == 2){
                            $categoryList = '';
                            $i = 0;
                            foreach(json_decode($coupon->categories) as $category){
                                $i++;
                                $couponArray[] = $category;
                                $thisCategory = Category::where('id', '=', $category)->first();
                                if($i > 1){
                                    $categoryList .= ', ';
                                }
                                $categoryList .= $thisCategory->name;
                            }
                        } else {
                            $i = 0;
                            foreach(json_decode($coupon->products) as $product){
                                $i++;
                                $couponArray[] = $product;
                            }
                        }
                    }
                }
            }
        }
        if(current_user()){
            $products = current_user()->cart;
        } else {
            $products = json_decode($request->cartcheckout);
            $checkCities = City::find($request->scity);
            if($checkCities->country_id != $currentCountry){
                $validatedDate->errors()->add('field', 'Shteti ku do dërgohet është i detyrueshëm');
                return redirect()->route('view.checkout')->withErrors($validatedDate)->withInput();
            }
        }
        if($products){
            $order = new Order();
            if(current_user()){
                $order->user_id = current_user()->id;
                $order->address_id = $request->addresses;
                $user = current_user();
            } else {
                $user = new User();
                $user->first_name = $request->fname;
                $user->last_name = $request->lname;
                $user->email = $request->email;
                $randomPassword = Str::random(8);
                $user->password = Hash::make($randomPassword);
                $user->phone = $request->phone;
                $user->address = $request->addresses;
                $user->zipcode = $request->zipcode;
                $user->city = $request->city;
                $user->country_id = $request->country;
                $user->save();
                $userId = $user->id;
                $order->user_id = $userId;
                Mail::to($user->email)->send(new UserRegisterOrder($user, $randomPassword));
    
                $address = new UserAddress();
                $address->user_id = $user->id;
                $address->name = $request->sname;
                $address->phone = $request->sphone;
                $address->address = $request->saddress;
                $address->address2 = ''; // Address2
                $address->zipcode = $request->szipcode;
                $address->city = $request->scity;
                $address->country_id = $currentCountry;
                $address->save();
                $addressId = $address->id;
                $order->address_id = $addressId;
            }
            $order->notes = $request->additionalinformation;
            $order->value = 0;
            $order->transport = 0;
            $order->save();
            foreach($products as $product){
                if(current_user()){
                    $currProduct = $product->product;
                    $prodId = $product->product_id;
                } else {
                    $currProduct = Product::where('id', '=', $product->id)->first();
                    $prodId = $product->id;
                }
                $vendorProducts[$currProduct->vendor_id][] = $product;
            }
            foreach($vendorProducts as $key=>$cvendors) {
                $transTextD = '';
                $transportMax = 0;
                $vtransPrice = 0;
                $vtotalPrice = 0;
                $vendOrder = new OrderVendor();
                $vendOrder->order_id = $order->id;
                $vendOrder->vendor_id = $key;
                $vendOrder->value = 0;
                $vendOrder->transport = 0;
                $vendOrder->save();
                foreach($cvendors as $product){
                    if(current_user()){
                        $currProduct = $product->product;
                        $prodId = $product->product_id;
                    } else {
                        $currProduct = Product::where('id', '=', $product->id)->first();
                        $prodId = $product->id;
                    }
                    if($currProduct->status == 1 && $currProduct->vstatus == 1){
                        $currentVariant = $currProduct->cartVariant($product->variant_id);
                        $noStock = false;
                        $hasOffer= false;
                        $stockFrom = 1;
                        $prodDisscount = 0;
                        $disscountType = 0;
                        if($currentVariant){
                            $prodPrice = $currentVariant->price ? $currentVariant->price : $currProduct->price;
                            $oldPrice = $prodPrice;
                            if($currProduct->offers($product->variant_id)){
                                $offer = $currProduct->offers($product->variant_id);
                                if($offer->type < 3){
                                    $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                                } else {
                                    $prodPrice = $offer->discount;
                                }
                                $prodDisscount = ($oldPrice - $prodPrice);
                                $disscountType = 1;
                                $hasOffer= true;
                            } else {
                                $offer = '';
                            }
                            if($currentVariant->stock){
                                $prodMaxStock = $currentVariant->stock;
                                $stockFrom = 2;
                            } else {
                                $prodMaxStock = $currProduct->stock;
                            }
                            $productStock = $product->qty;
                            if($prodMaxStock){
                                if($product->qty > $prodMaxStock){
                                    $productStock = $prodMaxStock;
                                }
                            } else {
                                $noStock = true;
                            }
                        } else {
                            $prodPrice = $currProduct->price;
                            $oldPrice = $prodPrice;
                            if($currProduct->offers()){
                                $offer = $currProduct->offers();
                                if($offer->type < 3){
                                    $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                                } else {
                                    $prodPrice = $offer->discount;
                                }
                                $prodDisscount = ($oldPrice - $prodPrice);
                                $disscountType = 1;
                                $hasOffer= true;
                            } else {
                                $offer = '';
                            }
                            $prodMaxStock = $currProduct->stock;
                            $productStock = $product->qty;
                            if($prodMaxStock){
                                if($product->qty > $prodMaxStock){
                                    $productStock = $prodMaxStock;
                                }
                            } else {
                                $productStock = 0;
                                $prodMaxStock = 0;
                                $noStock = true;
                            }
                        }
                        $getShipping = $currProduct->shippings->where('country_id', '=', $currentCountry)->first();
                        $shippingCTransport = false;
                        if(!$currProduct->shipping && $getShipping){
                            if($getShipping->free){
                                $shippingCost = 0;
                            } else {
                                $shippingCost = $getShipping->cost;
                            }
                            if($getShipping->shipping == 1){
                                $shippingCTransport = true;
                            }
                        } else {
                            $getShipping = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                            if($getShipping->cost){
                                $shippingCost = $getShipping->cost;
                            } else {
                                $shippingCost = 0;
                            }
                            if($getShipping->transport > 0){
                                $shippingCTransport = true;
                            }
                        }
                        
                        if(!$noStock && $getShipping && $shippingCTransport){
                            if($hasCoupon && $currProduct->owner->id ==  $coupon->vendor_id){
                                $couponDisscountPriceP = 0;
                                $calcCoupon = true;
                                if($hasOffer && $coupon->withoffer){
                                    if($coupon->action == 2){
                                        if($coupon->discount < $prodPrice){
                                            $couponPriceP = $prodPrice - $coupon->discount;
                                            $couponDisscountPriceP = $coupon->discount;
                                        } else {
                                            $couponPriceP = $prodPrice;
                                            $couponDisscountPriceP = 0;
                                            $calcCoupon = false;
                                        }
                                    } else {
                                        $couponPriceP = ($prodPrice * (100 - $coupon->discount))/100;
                                        $couponDisscountPriceP = $prodPrice - $couponPriceP;
                                    }
                                    $cprodDisscount = $couponDisscountPriceP;
                                    $cdisscountType = 2;
                                } else {
                                    if($coupon->action == 2){
                                        if($coupon->discount < $oldPrice){
                                            $couponPriceP = $oldPrice - $coupon->discount;
                                            $couponDisscountPriceP = $coupon->discount;
                                        } else {
                                            $couponPriceP = $oldPrice;
                                            $couponDisscountPriceP = 0;
                                            $calcCoupon = false;
                                        }
                                    } else {
                                        $couponPriceP = ($oldPrice * (100 - $coupon->discount))/100;
                                        $couponDisscountPriceP = $oldPrice - $couponPriceP;
                                    }
                                    $cprodDisscount = $couponDisscountPriceP;
                                    $cdisscountType = 3;
                                }
                                if($calcCoupon){
                                    if($coupon->type == 1){
                                        $prodPrice = $couponPriceP;
                                        ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                        $disscountType = $cdisscountType;
                                    } else if($coupon->type == 2){
                                        $productCategories = $currProduct->allCategories()->pluck('id')->toArray();
                                        $hasCategory = array_intersect($productCategories, $couponArray);
                                        if(count($hasCategory)){
                                            $prodPrice = $couponPriceP;
                                            ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                            $disscountType = $cdisscountType;
                                        }
                                    } else {
                                        if(in_array($currProduct->id, $couponArray)){
                                            $prodPrice = $couponPriceP;
                                            ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                            $disscountType = $cdisscountType;
                                        }
                                    }
                                }
                            }
    
                            $totalPrice += ($productStock * $prodPrice);
    
                            $vtotalPrice += ($productStock * $prodPrice);
                            $vtransPrice += $shippingCost;
                            if($transportMax < $shippingCost){
                                $transportMax = $shippingCost;
                            }
                            
                            $orderDetail = new OrderDetail();
                            $orderDetail->order_id = $order->id;
                            $orderDetail->order_vendor_id = $vendOrder->id;
                            $orderDetail->product_id = $prodId;
                            $orderDetail->variant_id = $product->variant_id;
                            $orderDetail->price = $prodPrice;
                            $orderDetail->qty = $productStock;
                            $orderDetail->personalize = (isset($product->personalize)?$product->personalize:'');
                            $orderDetail->ulje = $prodDisscount;
                            $orderDetail->tipi = $disscountType;
                            $orderDetail->save();
                            if($stockFrom == 2){
                                if($currentVariant && $currentVariant->stock){
                                    $thisVariant = ProductVariant::find($product->variant_id);
                                    $newStock = $currentVariant->stock - $productStock;
                                    $thisVariant->stock = $newStock;
                                    $thisVariant->save();
                                }
                            } else {
                                $newStock = $prodMaxStock - $productStock;
                                $currProduct->stock = $newStock;
                            }
                            $newSales = $currProduct->sales + $productStock;
                            $currProduct->sales = $newSales;
                            $currProduct->save();
                        }
                    }
                }
                $getShippingVendor = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                $transType = 1;
                if($getShippingVendor){
                    $transType = $getShippingVendor->transport;
                }
                $vendorTransPrice = 0;
                if($getShippingVendor){
                    if($transType == 2){
                        if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                            $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        } else {
                            $vendorTransPrice = $vtransPrice;
                            $transTotalPrice += $vtransPrice;
                        }
                    } else if($transType == 3){
                        $transTotalPrice += $transportMax;
                        $vendorTransPrice = $transportMax;
                        $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                    } else if($transType == 4){
                        $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                        if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                            $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        } else {
                            $transTotalPrice += $transportMax;
                            $vendorTransPrice = $transportMax;
                        }
                    } else {
                        $transTotalPrice += $vtransPrice;
                        $vendorTransPrice = $vtransPrice;
                    }
                }
                $vendOrder->value = $vtotalPrice;
                $vendOrder->transport = $vendorTransPrice;
                $vendOrder->save();
                // Mail::to($vendOrder->vendor->email)->send(new NewOrder($user, $order, $vendOrder));
            }
            $order->value = $totalPrice;
            $order->transport = $transTotalPrice;
            $order->save();
            if(current_user()){
                current_user()->cart()->delete();
                // Mail::to(current_user()->email)->send(new NewOrder($user, $order, false));
            } else {
                // Mail::to($user->email)->send(new NewOrder($user, $order, false));
            }
            if(current_user()){
                session()->put('success', 'ordersuccess','Porosia u krye me sukses. Ju do të njoftoheni me email ose mund të shihni në profilin tuaj përditsimet në lidhje me porosinë.');
                return redirect()->route('profile.orders.index');
            } else {
                session()->put('success','Porosia u krye me sukses. Ju do të njoftoheni me email ose mund të shihni në profilin tuaj përditsimet në lidhje me porosinë');
                session()->flash('clearlocal', 'true.');
                return redirect()->route('home');
            }
        }
    }

    public function trackorder()
    {
        $orderTrack = [];
        $orderTrackSearch = false;
        $error = false;
        return view('user.track.order', compact('orderTrack', 'orderTrackSearch'));
    }

    public function trackorderpost(Request $request)
    {
        $validatedDate = $request->validate([
            'ordernumber' => 'required',
            'email' => 'required',
        ], [
            'ordernumber.required' => 'Numri i Porosisë është i detyrueshëm',
            'email.required' => 'Email është i detyrueshëm',
        ]);
        if(is_numeric($request->ordernumber)){
            $order = Order::find($request->ordernumber);
            if($order){
                if($order->user->email == $request->email){
                    $orderVendor = OrderVendor::where('order_id', '=', $request->ordernumber)->get();
                    $orderTrack = OrderTrack::where('order_id', '=', $request->ordernumber)->get();
                    $orderTrackSearch = true;
                    $error = false;
                    return view('user.track.order', compact('order', 'orderTrack', 'orderVendor', 'orderTrackSearch', 'error'));
                } else {
                    $orderTrackSearch = true;
                    $orderTrack = '';
                    $error = true;
                    return view('user.track.order', compact('orderTrack', 'orderTrackSearch', 'error'));
                }
            } else {
                $orderTrackSearch = true;
                $orderTrack = '';
                $error = true;
                return view('user.track.order', compact('orderTrack', 'orderTrackSearch', 'error'));
            }
        } else {
            abort(404);
        }
    }
}
