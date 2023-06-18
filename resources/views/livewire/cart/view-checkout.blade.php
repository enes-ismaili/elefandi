<div>
    <div class="card checkout-total">
        <div class="card-header">
            <h5>Porosia juaj</h5>
            <input type="hidden" name="cartcheckout" id="cartCheckout" value="{{ json_encode($currentCart) }}">
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produkte</th>
                        <th>Sasia</th>
                        <th>Çmimi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $couponDisscountPrice = 0;
                    @endphp
                    @php
                        $allProducts = [];
                        $vendorProducts = [];
                        foreach($currentCart as $product){
                            $currProduct = App\Models\Product::where('id', '=', $product->id)->first();
                            $vendorProducts[$currProduct->vendor_id][] = $product;
                        }
                    @endphp
                    @foreach($vendorProducts as $cvendors)
                    @php
                        $transTextD = '';
                        $transportMax = 0;
                        $vtransPrice = 0;
                        $vtotalPrice = 0;
                    @endphp
                    @foreach($cvendors as $product)
                        @php
                            $currProduct = App\Models\Product::where('id', '=', $product->id)->first();
                        @endphp
						@if($product->qty && $product->qty > 0 && $currProduct->status == 1 && $currProduct->vstatus == 1)
                        @php
                            $currentVariant = $currProduct->cartVariant($product->variant_id);
                            $noStock = false;
                            $hasOffer = false;
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
                                    $hasOffer= true;
                                } else {
                                    $offer = '';
                                }
                                $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $currProduct->stock;
                                $productStock = $product->qty;
                                if($prodMaxStock){
                                    if($product->qty > $prodMaxStock){
                                        $productStock = $prodMaxStock;
                                    }
                                } else {
                                    $noStock = true;
                                }
                                $varName = $currentVariant->name;
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
                                $varName = '';
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
                            $hasCouponActive = false;
                            if($hasCoupon && !$noStock && (($getShipping && $getShipping->shipping == 1))){
                                if($currProduct->owner->id ==  $coupon->vendor_id){
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
                                    }
                                    if($calcCoupon){
                                        if($coupon->type == 1){
                                            $couponPrice = $couponPriceP;
                                            $couponDisscountPrice += $couponDisscountPriceP;
                                            $hasCouponActive = true;
                                            $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                        } else if($coupon->type == 2){
                                            $productCategories = $currProduct->allCategories()->pluck('id')->toArray();
                                            $hasCategory = array_intersect($productCategories, $couponArray);
                                            if(count($hasCategory)){
                                                $couponPrice = $couponPriceP;
                                                $couponDisscountPrice += $couponDisscountPriceP;
                                                $hasCouponActive = true;
                                                $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                            }
                                        } else {
                                            if(in_array($currProduct->id, $couponArray)){
                                                $couponPrice = $couponPriceP;
                                                $couponDisscountPrice += $couponDisscountPriceP;
                                                $hasCouponActive = true;
                                                $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if(!$noStock && $getShipping && $shippingCTransport){
                                $totalPrice += ($product->qty * $prodPrice);
                                $vtotalPrice += ($product->qty * $prodPrice);
                                $vtransPrice += $shippingCost;
                                if($transportMax < $shippingCost){
                                    $transportMax = $shippingCost;
                                }
                                // if(isset($transPriceVend[$currProduct->owner->id]) && $transPriceVend[$currProduct->owner->id]){
                                //     $transPriceVend[$currProduct->owner->id]['cost'] += $shippingCost;
                                // } else {
                                //     $transPriceVend[$currProduct->owner->id]['cost'] = $shippingCost;
                                //     $transPriceVend[$currProduct->owner->id]['name'] = $currProduct->owner->name;
                                // }
                                // $transPrice += $shippingCost;
                            }
                        @endphp
                        @if(!$noStock && $getShipping && $shippingCTransport)
                            <tr>
                                <td>
                                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">{{$currProduct->name}}</a>
                                    @if($varName)
                                        <br>
                                        <span>Varianti: <span>{{ $currentVariant->name }}</span></span>
                                    @endif
                                    @if(isset($product->personalize) && $product->personalize)
                                        <br>
                                        <span>Personalizim: <span>{{ $product->personalize }}</span></span>
                                    @endif
                                    <br>
                                    <span>Dyqani: 
                                        <a href="{{ route('single.vendor', $currProduct->owner->slug) }}">{!! strtoupper($currProduct->owner->name) !!}
                                            @if($currProduct->owner->verified)<img class="vendor-verification" title="Dyqan i verifikuar" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">@endif
                                        </a>
                                    </span>
                                </td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ number_format($prodPrice,2) }}€</td>
                            </tr>
                        @endif
						@endif
                    @endforeach
                        @php
                            $getShippingVendor = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                            $transType = 1;
                            if($getShippingVendor){
                                $transType = $getShippingVendor->transport;
                            }
                            if($getShippingVendor){
                                if($transType == 2){
                                    if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                        $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                                    } else {
                                        $transPrice += $vtransPrice;
                                    }
                                } else if($transType == 3){
                                    $transPrice += $transportMax;
                                    $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                                } else if($transType == 4){
                                    $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                                    if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                        $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                                    } else {
                                        $transPrice += $transportMax;
                                    }
                                } else {
                                    $transPrice += $vtransPrice;
                                }
                            }
                        @endphp
                    @endforeach
                </tbody>
            </table>
            <div class="divider"></div>
            <div class="tworows subtotal">
                <div class="left">Nëntotal</div>
                <div class="right"><span>{{ number_format($totalPrice,2) }}</span> €</div>
            </div>
            <div class="tworows subtotal">
                <div class="left">Kosto e transportit</div>
                <div class="right"><span>{{ number_format($transPrice,2) }}</span> €</div>
            </div>
            @if($couponDisscountPrice)
            <div class="tworows subtotal">
                <div class="left">Kuponi</div>
                <div class="right"><span>- {{ number_format($couponDisscountPrice,2) }}</span> €</div>
            </div>
            @endif
            <div class="tworows subtotal">
                <div class="left">Total</div>
                <div class="right"><span>{{ number_format(($totalPrice + $transPrice - $couponDisscountPrice),2) }}</span> €</div>
            </div>
            <div class="divider"></div>
            <div class="payment-options">
                <label for="">Metoda e Pagesës</label>
                <div>PARA NË DORË</div>
                <div>Paguaj pasi ta pranoni porosinë</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="checkbox">
            <input name="terms-and-conditions" class="form-control" type="checkbox" id="termsandconditions" required="">
            <label for="termsandconditions">Unë i pranoj Kushtet e Përdorimit dhe Politikat e Privatësisë</label>
        </div>
        <p id="termserror" style="color:red; display:none;"><i class="fa fa-exclamation-circle"></i> Ju lutem pranoni kushtet e përdorimit për të vazhduar!</p>
    </div>
    @if($totalPrice && $totalPrice >= 0)
        <button type="submit" class="btn full-width" >Përfundo blerjen</button>
    @else
        <button type="submit" class="btn c5 full-width" disabled >Përfundo blerjen</button>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            getCartss();
            // let cartCheckout = document.querySelector('#cartCheckout');
            // if(cartCheckout){
            //     console.log(cartCheckout)
            //     let currentCarts = window.localStorage.getItem('cart');
            //     console.log(currentCarts)
            //     cartCheckout.value = ''+currentCarts;
            // }
            // getCartss()
        });
        function getCartss(){
            let currentCart = window.localStorage.getItem('cart');
            if(currentCart){
                console.log('tesa')
                window.livewire.emit('cgetCart', currentCart);
                // let cartCheckout = document.querySelector('#cartCheckout');
                // cartCheckout.value = currentCart;
                // cartCheckout.value = 'tes';
            }
        }
    </script>
</div>
