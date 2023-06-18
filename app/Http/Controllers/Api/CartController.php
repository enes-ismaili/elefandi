<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Mail\NewOrder;
use App\Models\Coupon;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\OrderVendor;
use App\Models\UserAddress;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransportInfo;
use App\Mail\UserRegisterOrder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CartController extends BaseController
{
    public function getCarts(Request $request)
    {
        $productIds = json_decode($request->cart);
        $countryId = json_decode($request->country);
        if(!$countryId) {
            $countryId = 1;
        }
        $productCarts = [];
        if(!$productIds){
            return [];
        }
        foreach ($productIds as $key=>$value){
            $vendorId = substr($key, 1);
            $dVendor = Vendor::find($vendorId);
            $products = [];
            $subTotal = 0;
            $transport = 0;
            $transportMax = 0;
            $hasOffer = false;
			$getShippingVendor = $dVendor->shippings()->where('country_id', '=', $countryId)->first();
			$transType = 1;
			if($getShippingVendor){
				$transType = $getShippingVendor->transport;
			}
            foreach($value as $productss){
                foreach($productss as $product){
                    if($product && $product->id){
                        $hasTransport = true;
                        $hasStock = true;
                        $dproduct = Product::find($product->id);
                        if($dproduct){
                            if($product->variant != 0){
                                $currentVariant = $dproduct->cartVariant($product->variant);
                                if($currentVariant){
                                    $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = $dproduct->offers($product->variant);
                                    $varName = $currentVariant->name;
                                    $varId = $currentVariant->id;
                                    $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $dproduct->stock;
                                    $productStock = $product->qty;
                                } else {
                                    $prodPrice = $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = false;
                                    $varName = 'Është Hequr';
                                    $varId = $product->variant;
                                    $prodMaxStock = 0;
                                    $productStock = 0;
                                }
                            } else {
                                $prodPrice = $dproduct->price;
                                $oldPrice = $prodPrice;
                                $offer = $dproduct->offers();
                                $varName = '';
                                $varId = 0;
                                $prodMaxStock = $dproduct->stock;
                                $productStock = $product->qty;
                            }
                            if($offer){
                                if($offer->type < 3){
                                    $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                                    $offerDiscount = '-'.round($offer->discount, 1).'%';
                                    $hasOffer= true;
                                } else {
                                    if($offer->discount > 0){
                                        $offerPrice = $offer->discount;
                                        $offerDiscount = '-'.($prodPrice - $offer->discount).'€';
                                        $hasOffer= true;
                                    } else {
                                        $offerPrice = $prodPrice;
                                        $offerDiscount = 0;
                                        $hasOffer= false;
                                    }
                                }
                                
                                if($hasOffer && $prodPrice - $offer->discount){
                                    $offerDetail = [
                                        'offer' => true,
                                        'cost' => $oldPrice,
                                        'nprice' => $offerPrice,
                                        'discount' => $offerDiscount,
                                    ];
                                } else {
                                    $offerDetail = [
                                        'offer' => false,
                                        'cost' => $oldPrice,
                                        'nprice' => 0,
                                        'discount' => 0,
                                    ];
                                }
                            } else {
                                $offer = '';
                                $offerDetail = [
                                    'offer' => false,
                                    'cost' => $oldPrice,
                                    'nprice' => 0,
                                    'discount' => 0,
                                ];
                            }
                            if($prodMaxStock){
                                if($product->qty > $prodMaxStock){
                                    $productStock = $prodMaxStock;
                                }
                            } else {
                                $productStock = 0;
                                $hasStock = false;
                            }
                            $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                            if(!$dproduct->shipping && $getShipping){
                                $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                                $transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                                $productShippings = [
                                    "country" => $countryId,
                                    "shipping" => $getShipping->shipping,
                                    "free" => $getShipping->free,
                                    "cost" => $transportCost,
                                    "shipping_time" => $getShipping->shipping_time,
                                    "timeName" => $transportInfo->name,
                                ];
                                if($getShipping->shipping == 0){
                                    $hasTransport = false;
                                }
                            } else {
                                $getShippingVendor = $dproduct->owner->shippings()->where('country_id', '=', $countryId)->first();
                                if($getShippingVendor){
                                    $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                                    $transportCost = $getShippingVendor->cost;
                                    $productShippings = [
                                        "country" => $countryId,
                                        "shipping" => 1,
                                        "free" => ($getShippingVendor->cost) ? 0: 1,
                                        "cost" => ($getShippingVendor->cost) ?: 0,
                                        "shipping_time" => $getShippingVendor->transtime,
                                        "timeName" => (($transportInfo) ? $transportInfo->name : 'Nuk Transportohet në këtë Shtet' ),
                                    ];
                                    if($getShippingVendor->transport == 0){
                                        $hasTransport = false;
                                    }
                                } else {
                                    $productShippings = [
                                        "country" => $countryId,
                                        "shipping" => 0,
                                        "free" => 0,
                                        "cost" => 0,
                                        "shipping_time" => 0,
                                        "timeName" => 'Nuk Transportohet në këtë Shtet',
                                    ];
                                    $hasTransport = false;
                                }
                            }
                            if($dproduct->status!=1 || $dproduct->vstatus!=1){
                                $hasStock = false;
                                $productStock = 0;
                                $prodMaxStock = 0;
                            }
                            $productPersonalize = NULL;
                            if(isset($product->personalize)){
                                $productPersonalize = $product->personalize;
                            }
                            array_push($products, [
                                'id' => $dproduct->id,
                                'name' => $dproduct->name,
                                'image' => ((\File::exists('photos/products/70/'.$dproduct->image)) ? asset('/photos/products/70/'.$dproduct->image) :  asset('/photos/products/'.$dproduct->image)),
                                'personalize' => $productPersonalize,
                                'stock' => $prodMaxStock,
                                'qty' => $productStock,
                                'available' => (($hasTransport && $hasStock) ? true : false),
                                'price' => $offerDetail,
                                'variant' => $varName,
                                'variant_id' => $varId,
                                'shipping' => $productShippings,
                            ]);
                            if($hasTransport && $hasStock){
                                if($offerDetail['offer']){
                                    $subTotal += $offerPrice * $product->qty;
                                } else {
                                    $subTotal += $oldPrice * $product->qty;
                                }
                                
                                $transport += $transportCost;
                                if($transportMax < $transportCost){
                                    $transportMax = $transportCost;
                                }
                            }
                        }
                    }
                }
            }
			$fTransport = $transport;
            $transTextD = '';
            if($getShippingVendor){
				if($transType == 2){
                    if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                        $fTransport = 0;
                        $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                    }
				} else if($transType == 3){
					$fTransport = $transportMax;
                    $transTextD = $dVendor->name.' ofron transportin me te madh te nje prej produkteve dhe per pjesen tjeter falas';
				} else if($transType == 4){
					$fTransport = $transportMax;
                    $transTextD = $dVendor->name.' ofron transportin me te madh te nje prej produkteve dhe per pjesen tjeter falas';
                    if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                        $fTransport = 0;
                        $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                    }
				}
			}
            array_push($productCarts, [
                'id' => $dVendor->id,
                'vendCost' => 0,
                'image' => (($dVendor->logo_path) ? asset('/photos/vendor/'.$dVendor->logo_path) : ''),
                'name' => $dVendor->name,
                'vendType' => '',
                'verified' => $dVendor->verified,
                'products' => $products,
                'subTotal' => $subTotal,
                'transport' => $fTransport,
                'transportT' => $transTextD,
            ]);
        }
        return $productCarts;
    }

    public function getCheckout(Request $request)
    {
        $productIds = json_decode($request->cart);
        $countryId = json_decode($request->country);
        $couponCode = $request->coupon;
        if(!$countryId) {
            $countryId = 1;
        }
        $hasCoupon = false;
        $couponArray = [];
        if($couponCode){
            $coupon = Coupon::where('code', '=', $couponCode)->first();
            if($coupon){
                $today = time();
                $startDate = strtotime($coupon->start_date);
                $expireDate = strtotime($coupon->expire_date);
                if($today > $startDate && $today < $expireDate){
                    $hasCoupon = true;
                    $couponAction = ($coupon->action == 1) ? '%' : '€';
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
                    } else if($coupon->type == 3){
                        $i = 0;
                        foreach(json_decode($coupon->products) as $product){
                            $i++;
                            $couponArray[] = $product;
                        }
                    }
                }
            }
        }
        $productCarts = [];
        $totalProducts = 0;
        foreach ($productIds as $key=>$value){
            $couponDisscountPrice = 0;
            $vendorId = substr($key, 1);
            $dVendor = Vendor::find($vendorId);
            if($dVendor->vstatus==1){
                $products = [];
                $subTotal = 0;
                $osubTotal = 0;
                $transport = 0;
                $transportMax = 0;
                $hasOffer = false;
                $getShippingVendor = $dVendor->shippings()->where('country_id', '=', $countryId)->first();
                $transType = 1;
                if($getShippingVendor){
                    $transType = $getShippingVendor->transport;
                }
                foreach($value as $productss){
                    foreach($productss as $product){
                        $hasTransport = true;
                        $hasStock = true;
                        $dproduct = Product::find($product->id);
                        if($dproduct && $dproduct->status==1 && $dproduct->vstatus==1){
                            if($product->variant != 0){
                                $currentVariant = $dproduct->cartVariant($product->variant);
                                if($currentVariant){
                                    $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = $dproduct->offers($product->variant);
                                    $varName = $currentVariant->name;
                                    $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $dproduct->stock;
                                    $productStock = $product->qty;
                                } else {
                                    $prodPrice = $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = false;
                                    $varName = '';
                                    $prodMaxStock = 0;
                                    $productStock = 0;
                                }
                            } else {
                                $prodPrice = $dproduct->price;
                                $oldPrice = $prodPrice;
                                $offer = $dproduct->offers();
                                $varName = '';
                                $prodMaxStock = $dproduct->stock;
                                $productStock = $product->qty;
                            }
                            if($offer){
                                if($offer->type < 3){
                                    $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                                    $offerDiscount = '-'.round($offer->discount, 1).'%';
                                    $hasOffer= true;
                                } else {
                                    if($offer->discount > 0){
                                        $offerPrice = $offer->discount;
                                        $offerDiscount = '-'.($prodPrice - $offer->discount).'€';
                                        $hasOffer= true;
                                    } else {
                                        $offerPrice = $prodPrice;
                                        $offerDiscount = 0;
                                        $hasOffer= false;
                                    }
                                }
                                if($hasOffer && $prodPrice - $offer->discount){
                                    $offerDetail = [
                                        'offer' => true,
                                        'cost' => $oldPrice,
                                        'nprice' => $offerPrice,
                                        'discount' => $offerDiscount,
                                    ];
                                    $prodPrice = $offerPrice;
                                } else {
                                    $offerDetail = [
                                        'offer' => false,
                                        'cost' => $oldPrice,
                                        'nprice' => 0,
                                        'discount' => 0,
                                    ];
                                }
                            } else {
                                $offer = '';
                                $offerDetail = [
                                    'offer' => false,
                                    'cost' => $oldPrice,
                                    'nprice' => 0,
                                    'discount' => 0,
                                ];
                            }
                            $couponActiveArray = [
                                'hasCoupon' => false,
                                'nprice' => 0
                            ];
                            if($offer){
                                $origPrice = $offerPrice;
                            } else {
                                $origPrice = $oldPrice;
                            }
                            if($hasCoupon){
                                $hasCouponActive = false;
                                $couponPrice = 0;
                                if($dproduct->owner->id ==  $coupon->vendor_id){
                                    $couponDisscountPriceP = 0;
                                    $calcCoupon = true;
                                    if($hasOffer && $coupon->withoffer){
                                        $origPrice = $prodPrice;
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
                                    } else {
                                        $origPrice = $oldPrice;
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
                                    }
                                    if($calcCoupon){
                                        if($coupon->type == 1){
                                            $couponPrice = $couponPriceP;
                                            $couponDisscountPrice += $couponDisscountPriceP;
                                            $hasCouponActive = true;
                                            $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                        } else if($coupon->type == 2){
                                            $productCategories = $dproduct->allCategories()->pluck('id')->toArray();
                                                $hasCategory = array_intersect($productCategories, $couponArray);
                                                if(count($hasCategory)){
                                                    $couponPrice = $couponPriceP;
                                                    $couponDisscountPrice += $couponDisscountPriceP;
                                                    $hasCouponActive = true;
                                                    $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                                }
                                        } else if($coupon->type == 3){
                                            if(in_array($dproduct->id, $couponArray)){
                                                $couponPrice = $couponPriceP;
                                                $couponDisscountPrice += $couponDisscountPriceP;
                                                $hasCouponActive = true;
                                                $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                            }
                                        }
                                        if($couponPrice){
                                            $prodPrice = $couponPrice;
                                        }
                                        $couponActiveArray = [
                                            'hasCoupon' => $hasCouponActive,
                                            'nprice' => $couponPrice
                                        ];
                                    }
                                }
                            }
                            if($prodMaxStock){
                                if($product->qty > $prodMaxStock){
                                    $productStock = $prodMaxStock;
                                }
                            } else {
                                $productStock = 0;
                                $hasStock = false;
                            }
                            $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                            if(!$dproduct->shipping && $getShipping){
                                $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                                $transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                                $productShippings = [
                                    "country" => $countryId,
                                    "shipping" => $getShipping->shipping,
                                    "free" => $getShipping->free,
                                    "cost" => $transportCost,
                                    "shipping_time" => $getShipping->shipping_time,
                                    "timeName" => $transportInfo->name,
                                ];
                                if($getShipping->shipping == 0){
                                    $hasTransport = false;
                                }
                            } else {
                                if($getShippingVendor){
                                    $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                                    //$transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                                    $transportCost = ($getShippingVendor->cost) ?: 0;
                                    $productShippings = [
                                        "country" => $countryId,
                                        "shipping" => $getShippingVendor->transport,
                                        "free" => ($getShippingVendor->cost) ? 0: 1,
                                        "cost" => $transportCost,
                                        "shipping_time" => $getShippingVendor->transtime,
                                        "timeName" => (($transportInfo) ? $transportInfo->name : 'Nuk Transportohet në këtë Shtet' ),
                                    ];
                                    if($getShippingVendor->transport == 0){
                                        $hasTransport = false;
                                    }
                                } else {
                                    $productShippings = [
                                        "country" => $countryId,
                                        "shipping" => 0,
                                        "free" => 0,
                                        "cost" => 0,
                                        "shipping_time" => 0,
                                        "timeName" => 'Nuk Transportohet në këtë Shtet',
                                    ];
                                    $hasTransport = false;
                                }
                            }
                            if($hasTransport && $hasStock){
                                $productPersonalize = NULL;
                                if(isset($product->personalize)){
                                    $productPersonalize = $product->personalize;
                                }
                                array_push($products, [
                                    'id' => $dproduct->id,
                                    'name' => $dproduct->name,
                                    'image' => ((\File::exists('photos/products/70/'.$dproduct->image)) ? asset('/photos/products/70/'.$dproduct->image) :  asset('/photos/products/'.$dproduct->image)),
                                    'personalize' => $productPersonalize,
                                    'stock' => $prodMaxStock,
                                    'qty' => $productStock,
                                    'available' => (($hasTransport && $hasStock) ? true : false),
                                    'price' => $offerDetail,
                                    'variant' => $varName,
                                    'shipping' => $productShippings,
                                    'coupon' => $couponActiveArray,
                                ]);
                                $subTotal += $prodPrice * $product->qty;
                                $osubTotal += $origPrice * $product->qty;
                                $transport += $transportCost;
                                if($transportMax < $transportCost){
                                    $transportMax = $transportCost;
                                }
                            }
                        }
                    }
                }
                $fTransport = $transport;
                $transTextD = '';
                if($getShippingVendor){
                    if($transType == 2){
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                            $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        }
                    } else if($transType == 3){
                        $fTransport = $transportMax;
                        $transTextD = $dVendor->name.' ofron transportin me te madhe te nje prej produkteve dhe per pjesen tjeter falas';
                    } else if($transType == 4){
                        $fTransport = $transportMax;
                        $transTextD = $dVendor->name.' ofron transportin me te madhe te nje prej produkteve dhe per pjesen tjeter falas';
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                            $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        }
                    }
                }
                $totalProducts += count($products);
                array_push($productCarts, [
                    'id' => $dVendor->id,
                    'vendCost' => 0,
                    'image' => (($dVendor->logo_path) ? asset('/photos/vendor/'.$dVendor->logo_path) : ''),
                    'name' => $dVendor->name,
                    'vendType' => '',
                    'verified' => $dVendor->verified,
                    'products' => $products,
                    'products_c' => count($products),
                    'subTotal' => $subTotal,
                    'osubTotal' => $osubTotal,
                    'transport' => $fTransport,
                    'transportT' => $transTextD,
                    'coupon_discount' => $couponDisscountPrice
                ]);
            }
        }
        if($totalProducts){
            return $productCarts;
        } else {
            return ['status'=>'error', 'message'=>'Ska Produkte në Shportë'];
        }
    }

    public function getDirectCheckout(Request $request)
    {
        $productId = json_decode($request->product);
        $variantId = json_decode($request->variant);
        $prodQty = json_decode($request->qty);
        $prodPersonalize = $request->personalize;
        $countryId = json_decode($request->country);
        $couponCode = $request->coupon;
        if(!$countryId) {
            $countryId = 1;
        }
        $hasCoupon = false;
        $couponArray = [];
        if($couponCode){
            $coupon = Coupon::where('code', '=', $couponCode)->first();
            if($coupon){
                $today = time();
                $startDate = strtotime($coupon->start_date);
                $expireDate = strtotime($coupon->expire_date);
                if($today > $startDate && $today < $expireDate){
                    $hasCoupon = true;
                    $couponAction = ($coupon->action == 1) ? '%' : '€';
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
                    } else if($coupon->type == 3){
                        $i = 0;
                        foreach(json_decode($coupon->products) as $product){
                            $i++;
                            $couponArray[] = $product;
                        }
                    }
                }
            }
        }
        $productCarts = [];
        $totalProducts = 0;
        $dproduct = Product::find($productId);
        if($dproduct){
            $couponDisscountPrice = 0;
            $hasTransport = true;
            $hasStock = true;
            $products = [];
            $subTotal = 0;
            $osubTotal = 0;
            $transport = 0;
            $transportMax = 0;
            $hasOffer = false;
            $dVendor = $dproduct->owner;
            $getShippingVendor = $dVendor->shippings()->where('country_id', '=', $countryId)->first();
            $transType = 1;
            if($getShippingVendor){
                $transType = $getShippingVendor->transport;
            }
            if($dproduct && $dproduct->status==1 && $dproduct->vstatus==1){
                if($variantId != 0){
                    $currentVariant = $dproduct->cartVariant($variantId);
                    if($currentVariant){
                        $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                        $oldPrice = $prodPrice;
                        $offer = $dproduct->offers($variantId);
                        $varName = $currentVariant->name;
                        $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $dproduct->stock;
                        $productStock = $prodQty;
                    } else {
                        $prodPrice = $dproduct->price;
                        $oldPrice = $prodPrice;
                        $offer = false;
                        $varName = '';
                        $prodMaxStock = 0;
                        $productStock = 0;
                    }
                } else {
                    $prodPrice = $dproduct->price;
                    $oldPrice = $prodPrice;
                    $offer = $dproduct->offers();
                    $varName = '';
                    $prodMaxStock = $dproduct->stock;
                    $productStock = $prodQty;
                }
                if($offer){
                    if($offer->type < 3){
                        $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                        $offerDiscount = '-'.round($offer->discount, 1).'%';
                        $hasOffer= true;
                    } else {
                        if($offer->discount > 0){
                            $offerPrice = $offer->discount;
                            $offerDiscount = '-'.($prodPrice - $offer->discount).'€';
                            $hasOffer= true;
                        } else {
                            $offerPrice = $prodPrice;
                            $offerDiscount = 0;
                            $hasOffer= false;
                        }
                    }
                    if($hasOffer && $prodPrice - $offer->discount){
                        $offerDetail = [
                            'offer' => true,
                            'cost' => $oldPrice,
                            'nprice' => $offerPrice,
                            'discount' => $offerDiscount,
                        ];
                        $prodPrice = $offerPrice;
                    } else {
                        $offerDetail = [
                            'offer' => false,
                            'cost' => $oldPrice,
                            'nprice' => 0,
                            'discount' => 0,
                        ];
                    }
                } else {
                    $offer = '';
                    $offerDetail = [
                        'offer' => false,
                        'cost' => $oldPrice,
                        'nprice' => 0,
                        'discount' => 0,
                    ];
                }
                $couponActiveArray = [
                    'hasCoupon' => false,
                    'nprice' => 0
                ];
                if($offer){
                    $origPrice = $offerPrice;
                } else {
                    $origPrice = $oldPrice;
                }
                if($hasCoupon){
                    $hasCouponActive = false;
                    $couponPrice = 0;
                    if($dproduct->owner->id ==  $coupon->vendor_id){
                        $couponDisscountPriceP = 0;
                        $calcCoupon = true;
                        if($hasOffer && $coupon->withoffer){
                            $origPrice = $prodPrice;
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
                        } else {
                            $origPrice = $oldPrice;
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
                        }
                        if($calcCoupon){
                            if($coupon->type == 1){
                                $couponPrice = $couponPriceP;
                                $couponDisscountPrice += $couponDisscountPriceP;
                                $hasCouponActive = true;
                                $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                            } else if($coupon->type == 2){
                                $productCategories = $dproduct->allCategories()->pluck('id')->toArray();
                                    $hasCategory = array_intersect($productCategories, $couponArray);
                                    if(count($hasCategory)){
                                        $couponPrice = $couponPriceP;
                                        $couponDisscountPrice += $couponDisscountPriceP;
                                        $hasCouponActive = true;
                                        $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                    }
                            } else if($coupon->type == 3){
                                if(in_array($dproduct->id, $couponArray)){
                                    $couponPrice = $couponPriceP;
                                    $couponDisscountPrice += $couponDisscountPriceP;
                                    $hasCouponActive = true;
                                    $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                }
                            }
                            if($couponPrice){
                                $prodPrice = $couponPrice;
                            }
                            $couponActiveArray = [
                                'hasCoupon' => $hasCouponActive,
                                'nprice' => $couponPrice
                            ];
                        }
                    }
                }
                if($prodMaxStock){
                    if($prodQty > $prodMaxStock){
                        $productStock = $prodMaxStock;
                    }
                } else {
                    $productStock = 0;
                    $hasStock = false;
                }
                $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                if(!$dproduct->shipping && $getShipping){
                    $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                    $transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                    $productShippings = [
                        "country" => $countryId,
                        "shipping" => $getShipping->shipping,
                        "free" => $getShipping->free,
                        "cost" => $transportCost,
                        "shipping_time" => $getShipping->shipping_time,
                        "timeName" => $transportInfo->name,
                    ];
                    if($getShipping->shipping == 0){
                        $hasTransport = false;
                    }
                } else {
                    if($getShippingVendor){
                        $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                        //$transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                        $transportCost = ($getShippingVendor->cost) ?: 0;
                        $productShippings = [
                            "country" => $countryId,
                            "shipping" => $getShippingVendor->transport,
                            "free" => ($getShippingVendor->cost) ? 0: 1,
                            "cost" => $transportCost,
                            "shipping_time" => $getShippingVendor->transtime,
                            "timeName" => (($transportInfo) ? $transportInfo->name : 'Nuk Transportohet në këtë Shtet' ),
                        ];
                        if($getShippingVendor->transport == 0){
                            $hasTransport = false;
                        }
                    } else {
                        $productShippings = [
                            "country" => $countryId,
                            "shipping" => 0,
                            "free" => 0,
                            "cost" => 0,
                            "shipping_time" => 0,
                            "timeName" => 'Nuk Transportohet në këtë Shtet',
                        ];
                        $hasTransport = false;
                    }
                }
                if($hasTransport && $hasStock){
                    $productPersonalize = NULL;
                    if(isset($prodPersonalize)){
                        $productPersonalize = $prodPersonalize;
                    }
                    array_push($products, [
                        'id' => $dproduct->id,
                        'name' => $dproduct->name,
                        'image' => ((\File::exists('photos/products/70/'.$dproduct->image)) ? asset('/photos/products/70/'.$dproduct->image) :  asset('/photos/products/'.$dproduct->image)),
                        'personalize' => $productPersonalize,
                        'stock' => $prodMaxStock,
                        'qty' => $productStock,
                        'available' => (($hasTransport && $hasStock) ? true : false),
                        'price' => $offerDetail,
                        'variant' => $varName,
                        'shipping' => $productShippings,
                        'coupon' => $couponActiveArray,
                    ]);
                    $subTotal += $prodPrice * $prodQty;
                    $osubTotal += $origPrice * $prodQty;
                    $transport += $transportCost;
                    if($transportMax < $transportCost){
                        $transportMax = $transportCost;
                    }
                }
                $fTransport = $transport;
                $transTextD = '';
                if($getShippingVendor){
                    if($transType == 2){
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                            $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        }
                    } else if($transType == 3){
                        $fTransport = $transportMax;
                        // $transTextD = $dVendor->name.' ofron transportin me te madhe te nje prej produkteve dhe per pjesen tjeter falas';
                    } else if($transType == 4){
                        $fTransport = $transportMax;
                        // $transTextD = $dVendor->name.' ofron transportin me te madhe te nje prej produkteve dhe per pjesen tjeter falas';
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                            $transTextD = $dVendor->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                        }
                    }
                }
                $totalProducts += count($products);
                array_push($productCarts, [
                    'id' => $dVendor->id,
                    'vendCost' => 0,
                    'image' => (($dVendor->logo_path) ? asset('/photos/vendor/'.$dVendor->logo_path) : ''),
                    'name' => $dVendor->name,
                    'vendType' => '',
                    'verified' => $dVendor->verified,
                    'products' => $products,
                    'products_c' => count($products),
                    'subTotal' => $subTotal,
                    'osubTotal' => $osubTotal,
                    'transport' => $fTransport,
                    'transportT' => $transTextD,
                    'coupon_discount' => $couponDisscountPrice
                ]);
            }
        }
        if($totalProducts){
            return $productCarts;
        } else {
            return ['status'=>'error', 'message'=>'Ska Produkte në Shportë'];
        }
    }
	
	public function checkoutOrder(Request $request)
	{
		if(!$request->cart && !$request->country) {
            return ['status'=>'error', 'message'=> 'Ka ndodhur një gabim']; 
        }
        $productIds = json_decode($request->cart);
        $productCount = 0;
        foreach ($productIds as $key=>$value){
            $productCount += collect($value)->count();
        }
        if($productCount == 0){
            return ['status'=>'error', 'message'=> 'Zgjidhni së paku një produkt']; 
        }
        $countryId = json_decode($request->countryId);
        $couponCode = $request->coupon;
        if(!$countryId) {
            $countryId = 1;
        }
        if($request->logged != 'false'){
            if(!$request->selAddresses){
                return ['status'=>'error', 'message'=> 'Adresa ështe e detyruar'];  
            }
            $user = current_user();
            $userId = $user->id;
            $address = $user->addresses->where('id', '=', $request->selAddresses)->first();
            if(!$address){
                return ['status'=>'error', 'message'=> 'Adresa ështe e detyruar'];  
            }
            $addressId = $address->id;
            $statusMessage = 'Porosia u krye me sukses. Kontrolloni Profilin per te parë Statusin e Porosisë';
        } else {
            if(!$request->firstName && !$request->lastName && !$request->email && !$request->phone && !$request->address && !$request->country && !$request->city) {
                return ['status'=>'error', 'message'=> 'Plotësoni të gjitha të dhënat']; 
            }
            $checkUser = User::where('email', '=', $request->email)->count();
            if($checkUser){
                return ['status'=>'error', 'message'=> 'Emaili është në Përdorim'];
            } else {
                $zipcode = ($request->zipcode) ? $request->zipcode : '';
                $registerUser = $this->registerUserCheckout(
                    $request->firstName,
                    $request->lastName,
                    $request->email,
                    $request->phone,
                    $request->address,
                    $zipcode,
                    $request->city,
                    $request->country
                );
                $userId = $registerUser['userId'];
                $addressId = $registerUser['addressId'];
                $statusMessage = 'Porosia u krye me sukses. Kontrolloni Emailin per te parë Statusin e Porosisë';
            }
        }
		$order = new Order();
        $order->user_id = $userId;
        $order->address_id = $addressId;
        $order->notes = $request->additionalinformation;
        $order->value = 0;
        $order->transport = 0;
        $order->save();
        $hasCoupon = false;
        $couponArray = [];
        if($couponCode){
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
                    } else if($coupon->type == 3){
                        $i = 0;
                        foreach(json_decode($coupon->products) as $product){
                            $i++;
                            $couponArray[] = $product;
                        }
                    }
                }
            }
        }
        $productCarts = [];
        $oTotal = 0;
        $oTransport = 0;
        foreach ($productIds as $key=>$value){
            $couponDisscountPrice = 0;
            $vendorId = substr($key, 1);
            $dVendor = Vendor::find($vendorId);
            if($dVendor->vstatus==1){
                $products = [];
                $subTotal = 0;
                $transport = 0;
                $transportMax = 0;
                $hasOffer = false;
                $getShippingVendor = $dVendor->shippings()->where('country_id', '=', $countryId)->first();
                $transType = 1;
                if($getShippingVendor){
                    $transType = $getShippingVendor->transport;
                }
                $vendOrder = new OrderVendor();
                $vendOrder->order_id = $order->id;
                $vendOrder->vendor_id = $vendorId;
                $vendOrder->value = 0;
                $vendOrder->transport = 0;
                $vendOrder->save();
                foreach($value as $productss){
                    foreach($productss as $product){
                        $hasTransport = true;
                        $hasStock = true;
                        $dproduct = Product::find($product->id);
                        $stockGet = 1;
                        $prodDisscount = 0;
                        $disscountType = 0;
                        if($dproduct && $dproduct->status==1 && $dproduct->vstatus==1){
                            if($product->variant != 0){
                                $currentVariant = $dproduct->cartVariant($product->variant);
                                if($currentVariant){
                                    $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = $dproduct->offers($product->variant);
                                    $varId = $currentVariant->id;
                                    if($currentVariant->stock){
                                        $prodMaxStock = $currentVariant->stock;
                                        $stockGet = 2;
                                    } else {
                                        $prodMaxStock = $dproduct->stock;
                                    }
                                    $productStock = $product->qty;
                                } else {
                                    $prodPrice = $dproduct->price;
                                    $oldPrice = $prodPrice;
                                    $offer = false;
                                    $varId = 0;
                                    $prodMaxStock = 0;
                                    $productStock = 0;
                                }
                            } else {
                                $prodPrice = $dproduct->price;
                                $oldPrice = $prodPrice;
                                $offer = $dproduct->offers();
                                $varId = 0;
                                $prodMaxStock = $dproduct->stock;
                                $productStock = $product->qty;
                            }
                            if($offer){
                                if($offer->type < 3){
                                    $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                                    $hasOffer= true;
                                } else {
                                    if($offer->discount > 0){
                                        $offerPrice = $offer->discount;
                                        $hasOffer= true;
                                    } else {
                                        $offerPrice = $prodPrice;
                                        $hasOffer= false;
                                    }
                                }
                                if($hasOffer && $prodPrice - $offer->discount){
                                    $productPrice = $offerPrice;
                                    $prodPrice = $offerPrice;
                                    $prodDisscount = ($oldPrice - $prodPrice);
                                    $disscountType = 1;
                                } else {
                                    $productPrice = $oldPrice;
                                }
                            } else {
                                $offer = '';
                                $productPrice = $oldPrice;
                            }
                            if($hasCoupon){
                                if($dproduct->owner->id ==  $coupon->vendor_id){
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
                                            $productCategories = $dproduct->allCategories()->pluck('id')->toArray();
                                                $hasCategory = array_intersect($productCategories, $couponArray);
                                                if(count($hasCategory)){
                                                    $prodPrice = $couponPriceP;
                                                    ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                                    $disscountType = $cdisscountType;
                                                }
                                        } else if($coupon->type == 3){
                                            if(in_array($dproduct->id, $couponArray)){
                                                $prodPrice = $couponPriceP;
                                                ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                                $disscountType = $cdisscountType;
                                            }
                                        }
                                    }
                                }
                            }
                            if($prodMaxStock){
                                if($product->qty > $prodMaxStock){
                                    $productStock = $prodMaxStock;
                                }
                            } else {
                                $productStock = 0;
                                $hasStock = false;
                            }
                            $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                            if(!$dproduct->shipping && $getShipping){
                                $transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                                if($getShipping->shipping == 0){
                                    $hasTransport = false;
                                }
                            } else {
                                if($getShippingVendor){
                                    $transportCost = ($getShippingVendor->cost) ? 0: 1;
                                    if($getShippingVendor->transport == 0){
                                        $hasTransport = false;
                                    }
                                } else {
                                    $hasTransport = false;
                                }
                            }
                            if($hasTransport && $hasStock){
                                $productPersonalize = NULL;
                                if(isset($product->personalize)){
                                    $productPersonalize = $product->personalize;
                                }
                                $subTotal += $prodPrice * $product->qty;
                                $transport += $transportCost;
                                if($transportMax < $transportCost){
                                    $transportMax = $transportCost;
                                }
                                $this->newOrderDetail($order->id,$vendOrder->id,$dproduct->id,$varId,$prodPrice,$productStock,$productPersonalize,$stockGet,$prodDisscount,$disscountType);
                            }
                        }
                    }
                }
                $fTransport = $transport;
                if($getShippingVendor){
                    if($transType == 2){
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                        }
                    } else if($transType == 3){
                        $fTransport = $transportMax;
                    } else if($transType == 4){
                        $fTransport = $transportMax;
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                        }
                    }
                }
                $vendOrder->value = $subTotal;
                $vendOrder->transport = $fTransport;
                $vendOrder->save();
                // Mail::to($vendOrder->vendor->email)->send(new NewOrder($user, $order, $vendOrder));
                $oTotal += $subTotal;
                $oTransport += $fTransport;
            }
        }
        $order->value = $oTotal;
        $order->transport = $oTransport;
        $order->save();
        // Mail::to($user->email)->send(new NewOrder($user, $order, false));
        return ['status'=>'success', 'message'=> $statusMessage]; 
	}

	public function checkoutDirectOrder(Request $request)
	{
        if(!$request->product && !$request->country) {
            return ['status'=>'error', 'message'=> 'Ka ndodhur një gabim']; 
        }
        $productId = json_decode($request->product);
        $variantId = json_decode($request->variant);
        $prodQty = json_decode($request->qty);
        $prodPersonalize = $request->personalize;
        
        $countryId = json_decode($request->countryId);
        $couponCode = $request->coupon;
        if(!$countryId) {
            $countryId = 1;
        }
        if($request->logged != 'false'){
            if(!$request->selAddresses){
                return ['status'=>'error', 'message'=> 'Adresa ështe e detyruar'];  
            }
            $user = current_user();
            $userId = $user->id;
            $address = $user->addresses->where('id', '=', $request->selAddresses)->first();
            if(!$address){
                return ['status'=>'error', 'message'=> 'Adresa ështe e detyruar'];  
            }
            $addressId = $address->id;
            $statusMessage = 'Porosia u krye me sukses. Kontrolloni Profilin per te parë Statusin e Porosisë';
        } else {
            if(!$request->firstName || !$request->lastName || !$request->email || !$request->phone || !$request->address || !$request->country || !$request->city) {
                return ['status'=>'error', 'message'=> 'Plotësoni të gjitha të dhënat']; 
            }
            $checkUser = User::where('email', '=', $request->email)->count();
            if($checkUser){
                return ['status'=>'error', 'message'=> 'Emaili është në Përdorim'];
            } else {
                $zipcode = ($request->zipcode) ? $request->zipcode : '';
                $registerUser = $this->registerUserCheckout(
                    $request->firstName,
                    $request->lastName,
                    $request->email,
                    $request->phone,
                    $request->address,
                    $zipcode,
                    $request->city,
                    $request->country
                );
                $userId = $registerUser['userId'];
                $addressId = $registerUser['addressId'];
                $statusMessage = 'Porosia u krye me sukses. Kontrolloni Emailin per te parë Statusin e Porosisë';
            }
        }
		$order = new Order();
        $order->user_id = $userId;
        $order->address_id = $addressId;
        $order->notes = $request->additionalinformation;
        $order->value = 0;
        $order->transport = 0;
        $order->save();
        $hasCoupon = false;
        $couponArray = [];
        if($couponCode){
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
                    } else if($coupon->type == 3){
                        $i = 0;
                        foreach(json_decode($coupon->products) as $product){
                            $i++;
                            $couponArray[] = $product;
                        }
                    }
                }
            }
        }
        $productCarts = [];
        $dproduct = Product::find($productId);
        $oTotal = 0;
        $oTransport = 0;
        if($dproduct){
            $couponDisscountPrice = 0;
            $hasTransport = true;
            $hasStock = true;
            $products = [];
            $subTotal = 0;
            $osubTotal = 0;
            $transport = 0;
            $transportMax = 0;
            $hasOffer = false;
            $dVendor = $dproduct->owner;
            $vendorId = $dVendor->id;
            $getShippingVendor = $dVendor->shippings()->where('country_id', '=', $countryId)->first();
            $transType = 1;
            if($getShippingVendor){
                $transType = $getShippingVendor->transport;
            }
            if($dVendor->vstatus==1){
                $vendOrder = new OrderVendor();
                $vendOrder->order_id = $order->id;
                $vendOrder->vendor_id = $vendorId;
                $vendOrder->value = 0;
                $vendOrder->transport = 0;
                $vendOrder->save();
                $stockGet = 1;
                $prodDisscount = 0;
                $disscountType = 0;
                if($dproduct && $dproduct->status==1 && $dproduct->vstatus==1){
                    if($variantId != 0){
                        $currentVariant = $dproduct->cartVariant($variantId);
                        if($currentVariant){
                            $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                            $oldPrice = $prodPrice;
                            $offer = $dproduct->offers($variantId);
                            $varId = $currentVariant->id;
                            if($currentVariant->stock){
                                $prodMaxStock = $currentVariant->stock;
                                $stockGet = 2;
                            } else {
                                $prodMaxStock = $dproduct->stock;
                            }
                            $productStock = $prodQty;
                        } else {
                            $prodPrice = $dproduct->price;
                            $oldPrice = $prodPrice;
                            $offer = false;
                            $varId = 0;
                            $prodMaxStock = 0;
                            $productStock = 0;
                        }
                    } else {
                        $prodPrice = $dproduct->price;
                        $oldPrice = $prodPrice;
                        $offer = $dproduct->offers();
                        $varId = 0;
                        $prodMaxStock = $dproduct->stock;
                        $productStock = $prodQty;
                    }
                    if($offer){
                        if($offer->type < 3){
                            $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                            $hasOffer= true;
                            $prodDisscount = ($oldPrice - $offerPrice);
                            $disscountType = 1;
                        } else {
                            if($offer->discount > 0){
                                $offerPrice = $offer->discount;
                                $hasOffer= true;
                                $prodDisscount = ($oldPrice - $offerPrice);
                                $disscountType = 1;
                            } else {
                                $offerPrice = $prodPrice;
                                $hasOffer= false;
                            }
                        }
                        if($hasOffer && $prodPrice - $offer->discount){
                            $productPrice = $offerPrice;
                            $prodPrice = $offerPrice;
                        } else {
                            $productPrice = $oldPrice;
                        }
                    } else {
                        $offer = '';
                        $productPrice = $oldPrice;
                    }
                    if($hasCoupon){
                        $hasCouponActive = false;
                        $couponPrice = 0;
                        if($dproduct->owner->id ==  $coupon->vendor_id){
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
                                    $couponPrice = $couponPriceP;
                                    ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                    $disscountType = $cdisscountType;
                                    $hasCouponActive = true;
                                } else if($coupon->type == 2){
                                    $productCategories = $dproduct->allCategories()->pluck('id')->toArray();
                                        $hasCategory = array_intersect($productCategories, $couponArray);
                                        if(count($hasCategory)){
                                            $couponPrice = $couponPriceP;
                                            ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                            $disscountType = $cdisscountType;
                                            $hasCouponActive = true;
                                        }
                                } else if($coupon->type == 3){
                                    if(in_array($dproduct->id, $couponArray)){
                                        $couponPrice = $couponPriceP;
                                        ($cdisscountType == 2) ? $prodDisscount += $cprodDisscount : $prodDisscount = $cprodDisscount;
                                        $disscountType = $cdisscountType;
                                        $hasCouponActive = true;
                                    }
                                }
                                if($couponPrice){
                                    $prodPrice = $couponPrice;
                                }
                                if($hasCouponActive){
                                    $productPrice = $couponPrice;
                                }
                            }
                        }
                    }
                    if($prodMaxStock){
                        if($prodQty > $prodMaxStock){
                            $productStock = $prodMaxStock;
                        }
                    } else {
                        $productStock = 0;
                        $hasStock = false;
                    }
                    $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                    if(!$dproduct->shipping && $getShipping){
                        $transportCost = ($getShipping->free) ? 0 : $getShipping->cost;
                        if($getShipping->shipping == 0){
                            $hasTransport = false;
                        }
                    } else {
                        if($getShippingVendor){
                            $transportCost = ($getShippingVendor->cost) ?: 0;
                            if($getShippingVendor->transport == 0){
                                $hasTransport = false;
                            }
                        } else {
                            $hasTransport = false;
                        }
                    }
                    if($hasTransport && $hasStock){
                        $productPersonalize = NULL;
                        if(isset($prodPersonalize)){
                            $productPersonalize = $prodPersonalize;
                        }
                        $subTotal += $prodPrice * $prodQty;
                        $transport += $transportCost;
                        if($transportMax < $transportCost){
                            $transportMax = $transportCost;
                        }
                        $this->newOrderDetail($order->id,$vendOrder->id,$dproduct->id,$varId,$productPrice,$productStock,$productPersonalize,$stockGet,$prodDisscount,$disscountType);
                    }
                }
                $fTransport = $transport;
                if($getShippingVendor){
                    if($transType == 2){
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                        }
                    } else if($transType == 3){
                        $fTransport = $transportMax;
                    } else if($transType == 4){
                        $fTransport = $transportMax;
                        if($getShippingVendor->limit && $subTotal >= $getShippingVendor->limit) {
                            $fTransport = 0;
                        }
                    }
                }
                $vendOrder->value = $subTotal;
                $vendOrder->transport = $fTransport;
                $vendOrder->save();
                // Mail::to($vendOrder->vendor->email)->send(new NewOrder($user, $order, $vendOrder));
                $oTotal += $subTotal;
                $oTransport += $fTransport;
            }
        }
        $order->value = $oTotal;
        $order->transport = $oTransport;
        $order->save();
        // Mail::to($user->email)->send(new NewOrder($user, $order, false));
        return ['status'=>'success', 'message'=> $statusMessage]; 
	}

    protected function newOrderDetail($orderId,$vendorId,$prodId,$varId,$productPrice,$productStock,$productPersonalize,$stockGet,$prodDisscount,$disscountType){
        $orderDetail = new OrderDetail();
        $orderDetail->order_id = $orderId;
        $orderDetail->order_vendor_id = $vendorId;
        $orderDetail->product_id = $prodId;
        $orderDetail->variant_id = $varId;
        $orderDetail->price = $productPrice;
        $orderDetail->qty = $productStock;
        $orderDetail->personalize = $productPersonalize;
        $orderDetail->ulje = $prodDisscount;
        $orderDetail->tipi = $disscountType;
        $orderDetail->save();
        if($stockGet == 2){
            $currentProduct = ProductVariant::find($varId);
            $newStock = $currentProduct->stock - $productStock;
            $currentProduct->stock = $newStock;
            $currentProduct->save();
            $currentProductB = Product::find($prodId);
            $newSales = $currentProductB->sales + $productStock;
            $currentProductB->sales = $newSales;
            $currentProductB->save();
        } else {
            $currentProduct = Product::find($prodId);
            $newStock = $currentProduct->stock - $productStock;
            $currentProduct->stock = $newStock;
            $newSales = $currentProduct->sales + $productStock;
            $currentProduct->sales = $newSales;
            $currentProduct->save();
        }
    }

    protected function registerUserCheckout($firstName,$lastName,$email,$phone,$addres,$zipcode,$city,$country){
        $user = new User();
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;
        $randomPassword = Str::random(8);
        $user->password = Hash::make($randomPassword);
        $user->phone = $phone;
        $user->address = $addres;
        $user->zipcode = $zipcode;
        $user->city = $city;
        $user->country_id = $country;
        $user->save();
        // $user->sendEmailVerificationNotification();
        // Mail::to($user->email)->send(new UserRegisterOrder($user, $randomPassword)); //TODO: Send email to user

        $address = new UserAddress();
        $address->user_id = $user->id;
        $address->name = $firstName.' '.$lastName;
        $address->phone = $phone;
        $address->address = $addres;
        $address->address2 = ''; // Address2
        $address->zipcode = $zipcode;
        $address->city = $city;
        $address->country_id = $country;
        $address->save();
        return ['userId' => $user->id, 'addressId' => $address->id];
    }
}