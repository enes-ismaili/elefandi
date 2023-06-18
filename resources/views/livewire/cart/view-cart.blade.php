<div>
    <table class="table shopping-cart">
        <thead>
            <tr>
                <th style="text-align: left;">Emri produktit</th>
                <th>Çmimi</th>
                <th>Sasia</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="cart-body">
            @php
                $totalPrice = 0;
                $transPrice = 0;
                $couponDisscountPrice = 0;
                $hasTProducts = false;
            @endphp
            @php
                $allProducts = [];
                $vendorProducts = [];
                foreach($products as $product){
                    if($getFromJson){
                        $currProduct = App\Models\Product::where('id', '=', $product->id)->first();
                    } else {
                        $currProduct = $product->product;
                    }
                    $vendorProducts[$currProduct->vendor_id][] = $product;
                }
            @endphp
            @foreach($vendorProducts as $cvendors)
                @php
                    $transTextD = '';
                    $transportMax = 0;
                    $vtransPrice = 0;
                    $vtotalPrice = 0;
                    $hasProducts = false;
                @endphp
                @foreach($cvendors as $product)
                    @php
                        if($getFromJson){
                            $currProduct = App\Models\Product::where('id', '=', $product->id)->first();
                        } else {
                            $currProduct = $product->product;
                        }
                    @endphp
					@if($product->qty && $product->qty > 0 && $currProduct->status == 1 && $currProduct->vstatus == 1)
                    @php
                        $noStock = false;
                        $hasOffer = false;
                        $currentVariant = '';
                        if($product->variant_id){
                            $currentVariant = $currProduct->cartVariant($product->variant_id);
                        }
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
                        if($hasCoupon && !$noStock && $shippingCTransport){
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
                        
                        if(!$noStock && (($getShipping && $shippingCTransport))){
                            $totalPrice += ($product->qty * $prodPrice);
                            $vtotalPrice += ($product->qty * $prodPrice);
                            $vtransPrice += $shippingCost;
                            if($transportMax < $shippingCost){
                                $transportMax = $shippingCost;
                            }
                            $hasProducts = true;
                            $hasTProducts = true;
                        }
                    @endphp
                    <tr class="single-cart @if($noStock)no-stock @endif @if(!$getShipping || !$shippingCTransport)no-shipping @endif" id="p{{$product->id}}v{{$product->variant_id}}">
                        <td>
                            <p style="display:none;" class="nochouttext">Ky produkt nuk mund të transportohet në lokacionin që keni zgjedhur.</p>
                            <div class="product-info">
                                <div class="thumbnail" >
                                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">
                                        <img src="{{asset('/photos/products/'.$currProduct->image)}}" alt="">
                                    </a>
                                </div>
                                <div class="content">
                                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">{{$currProduct->name}}</a>
                                    @if($varName)
                                        <p class="my-0">Varianti: <span>{{ $currentVariant->name }}</span></p>
                                    @endif
                                    @if(isset($product->personalize))
                                        <p class="my-0">Personalizim: <span>{{ $product->personalize }}</span></p>
                                    @endif
                                    <p class="mb-0">Dyqani:<strong><a href="{{ route('single.vendor', $currProduct->owner->slug) }}">{!! strtoupper($currProduct->owner->name) !!}
                                        @if($currProduct->owner->verified)<img class="vendor-verification" title="Dyqan i verifikuar" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">@endif
                                    </a></strong></p>
                                    <p class="my-0">Transporti:<strong>{{($shippingCost)?$shippingCost.'€' : 'Falas'}}</strong></p>
                                </div>
                            </div>
                        </td>
                        @if($offer)
                            <td class="price"><span>{{ number_format($prodPrice, 2).'€'}}<i>{{ ($hasCouponActive)? ' ('.$couponDisscount.')' : '' }}</i></span><span class="old_price">{{ number_format($oldPrice, 2).'€'}}</span><input type="hidden" class="tprice" value="{{$prodPrice}}"></td>
                        @elseif($hasCouponActive)
                            <td class="price"><span>{{ number_format($prodPrice, 2).'€'}} <i>({{ $couponDisscount }})</i></span><span class="old_price">{{ number_format($oldPrice, 2).'€'}}</span><input type="hidden" class="tprice" value="{{$prodPrice}}"></td>
                        @else
                            <td class="price"><span>{{ number_format($prodPrice, 2).'€'}}</span><input type="hidden" class="tprice" value="{{$prodPrice}}"></td>
                        @endif
                        <td>
                            <div class="cart-stock">
                                @if($noStock)
                                    <div class="stock-options">
                                        <div class="stock-show"><input type="number" name="tes" min="0" max="{{$prodMaxStock}}" value="{{$productStock}}" class="tqty"></div>
                                    </div>
                                    <span class="stock-info">Ska stok</span>
                                @else
                                    @if($isLogged)
                                    <div class="stock-options">
                                        <div class="stock-btn remove" wire:click="removeCartQty({{$product->id}}, {{$prodMaxStock}})">-</div>
                                        <div class="stock-show"><input type="number" name="tes" min="1" max="{{$prodMaxStock}}" value="{{ $productStock }}" class="tqty"></div>
                                        <div class="stock-btn add" wire:click="addCartQty({{$product->id}}, {{$prodMaxStock}})">+</div>
                                    </div>
                                    @else
                                    <div class="stock-options">
                                        <div class="stock-btn remove">-</div>
                                        <div class="stock-show"><input type="number" name="tes" min="1" max="{{$prodMaxStock}}" value="{{ $productStock }}" class="tqty"></div>
                                        <div class="stock-btn add">+</div>
                                    </div>
                                    @endif
                                    <span class="stock-info">(Gjithsej {{$prodMaxStock}} artikuj në stok)</span>
                                @endif
                            </div>
                        </td>
                        <td><span class="total-product">{{ number_format(($prodPrice * $productStock), 2) }}€</span></td>
                        <td>
                            @if($isLogged)
                                <span wire:click="removeProduct({{$product->id}})"><i class="fas fa-times"></i></span>
                            @else
                                <span class="remove-cart"><i class="fas fa-times"></i></span>
                            @endif
                        </td>
                    </tr>
					@endif
                @endforeach
                @php
                    $getShippingVendor = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                    $transType = 1;
                    if($getShippingVendor){
                        $transType = $getShippingVendor->transport;
                    }
                    if($getShippingVendor){
                        $prodOwnerName = strtoupper($currProduct->owner->name);
                        if($transType == 2){
                            if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                $transTextD = $prodOwnerName.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                            } else {
                                $transPrice += $vtransPrice;
                            }
                        } else if($transType == 3){
                            $transPrice += $transportMax;
                            $transTextD = $prodOwnerName.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                        } else if($transType == 4){
                            $transTextD = $prodOwnerName.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                            if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                $transTextD = $prodOwnerName.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                            } else {
                                $transPrice += $transportMax;
                            }
                        } else {
                            $transPrice += $vtransPrice;
                        }
                    }
                @endphp
                @if($transTextD && $hasProducts)
                    <tr><td style="color: #1da31b;">{{ $transTextD }}</td></tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @if(count($products) == 0)
        <h2 class="noproducts tcenter my-50" @if(!$isLogged)style="display:none;"@endif>Ju nuk keni artikuj në shportë!</h2>
    @endif
    @if(count($products) > 0 && !$hasTProducts)
        <h2 class="noproducts tcenter my-50" @if(!$isLogged)style="display:none;"@endif>Produktet që ju keni zgjedhur nuk mund të dërgohen!</h2>
    @endif
    {{-- <h2 class="noproducts tcenter mb-2" style="display:none;">Ju nuk keni artikuj në shportë!</h2> --}}
    <div class="cart-footer">
        @if($hasCoupon || $showCouponMsg)
            <p>{{ $couponText }}</p>
        @endif
        <div class="row">
            <div class="col-4">
                <div class="form-group prelative">
                    <label for="kuponkodi">Kupon</label>
                    <input class="form-control" id="kuponkodi" name="kuponkodi" type="text" placeholder="Shkruaj kodin e kuponit" wire:model.defer="couponCode">
                    @if($hasCoupon)
                        <span class="remove-coupon" wire:click="removeCoupon"><i class="fas fa-times"></i></span>
                    @endif
                </div>
                @if(!$hasCoupon)
                    <div class="form-group">
                        <button type="button" class="btn" wire:click="addCoupon()">Apliko kuponin</button>
                    </div>
                @endif
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="zonaedergesesselect">Shteti ku do dërgohet</label>
                    <select class="form-control" id="zonaedergesesselect" wire:model="currentCountry">
                        @foreach($shippingCountry as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="card shipping-total">
                    <div class="card-header">
                        <div class="tworows subtotal">
                            <div class="left">Nëntotal</div>
                            <div class="right"><span>{{ number_format($totalPrice, 2) }}</span> €</div>
                        </div>
                        <div class="tworows transport">
                            <div class="left">Kosto e transportit</div>
                            <div class="right"><span>{{ number_format($transPrice, 2) }}</span> €</div>
                        </div>
                        @if($couponDisscountPrice)
                        <div class="tworows coupon">
                            <div class="left">Kuponi</div>
                            <div class="right"><span>- {{ number_format($couponDisscountPrice, 2) }}</span> €</div>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="tworows totals">
                            <div class="left">Total</div>
                            <div class="right"><span>{{ number_format(($totalPrice + $transPrice - $couponDisscountPrice), 2) }}</span> €</div>
                        </div>
                    </div>
                </div>
                @if(count($products) > 0 && $hasTProducts)
                    <a href="{{ route('view.checkout') }}" class="btn full-width">Përfundo blerjen</a>
                @else
                    <button class="btn full-width c5" disabled>Përfundo blerjen</button>
                @endif
            </div>
        </div>
    </div>
    <script>
        @if(!$isLogged)
            document.addEventListener("DOMContentLoaded", () => {
                let noProducts = document.querySelector('.noproducts');
                // document.addEventListener("livewire:load", function(event) {
                //     noProducts = document.querySelector('.noproducts');
                //     if(noProducts){
                //         setTimeout(function(){
                //             noProducts.style.display = 'block';
                //         }, 500);
                //     }
                //     getCartss();
                //     console.log('cart');
                // });
                noProducts = document.querySelector('.noproducts');
                if(noProducts){
                    setTimeout(function(){
                        noProducts.style.display = 'block';
                    }, 500);
                }
                getCartss();
                console.log('cart');
                window.livewire.on('getCart', () => {
                    getCartss();
                });
                window.livewire.on('getCartLoad', () => {
                    countTotal()
                    let addStocks = document.querySelectorAll('.stock-btn.add');
                    let removeStocks = document.querySelectorAll('.stock-btn.remove');
                    let removeCarts = document.querySelectorAll('.remove-cart');
                    let subTotal = document.querySelector('.subtotal .right span');
                    let currentCartNow = window.localStorage.getItem('cart');
                    let newCartNow = {};
                    if(currentCartNow){
                        newCartNow = JSON.parse(currentCartNow);
                    }
                    addStocks.forEach(addStock => {
                        addStock.addEventListener('click', (addstock)=> {
                            let parentEl = addstock.target.parentElement;
                            let thisInput = parentEl.querySelector('input');
                            if(parseInt(thisInput.value) < parseInt(thisInput.max)) {
                                thisInput.value = parseInt(thisInput.value) + 1;
                                let rootEl = parentEl.parentElement.parentElement.parentElement;
                                let priceInp = rootEl.querySelector('.tprice').value;
                                rootEl.querySelector('.total-product').innerHTML = ((parseInt(thisInput.value) * parseFloat(priceInp))).toFixed(2)+'€';
                                subTotal.innerHTML = (parseFloat(subTotal.innerHTML) + parseFloat(priceInp)).toFixed(2);
                                countTotal();
                                newCartNow[rootEl.id]['qty'] = parseInt(thisInput.value);
                                window.localStorage.setItem('cart', JSON.stringify(newCartNow));
                            }
                        })
                    })
                    removeStocks.forEach(removeStock => {
                        removeStock.addEventListener('click', (removestock)=> {
                            let parentEl = removestock.target.parentElement;
                            let thisInput = parentEl.querySelector('input');
                            if(parseInt(thisInput.value) > parseInt(thisInput.min)) {
                                thisInput.value = parseInt(thisInput.value) - 1;
                                let rootEl = parentEl.parentElement.parentElement.parentElement;
                                let priceInp = rootEl.querySelector('.tprice').value;
                                rootEl.querySelector('.total-product').innerHTML = ((parseInt(thisInput.value) * parseFloat(priceInp))).toFixed(2)+'€';
                                subTotal.innerHTML = (parseFloat(subTotal.innerHTML) - parseFloat(priceInp)).toFixed(2);
                                countTotal();
                                newCartNow[rootEl.id]['qty'] = parseInt(thisInput.value);
                                window.localStorage.setItem('cart', JSON.stringify(newCartNow));
                            }
                        })
                    })
                    removeCarts.forEach(removeCart => {
                        removeCart.addEventListener('click', (removecart)=> {
                            let rootEl = removecart.target.parentElement.parentElement;
                            let thisPrice = rootEl.querySelector('.tprice').value;
                            let thisQty = rootEl.querySelector('.tqty').value;
                            let currentCartOld = window.localStorage.getItem('cart');
                            if(currentCartOld){
                                newCart = JSON.parse(currentCartOld);
                                delete newCart[rootEl.id];
                                let newCartString = JSON.stringify(newCart);
                                window.localStorage.setItem('cart', newCartString);
                                subTotal.innerHTML = (parseFloat(subTotal.innerHTML) - (parseFloat(thisPrice) * parseInt(thisQty))).toFixed(4);
                                countTotal();
                                rootEl.remove();
                                window.livewire.emitTo('header.mini-cart', 'getCarts', newCartString);
                            }
                            console.log(rootEl);
                        });
                    })
                });
            });
            function countTotal(){
                let subTotal = document.querySelector('.subtotal .right span');
                let transportCost = document.querySelector('.transport .right span');
                let coupon = document.querySelector('.coupon .right span');
                let totalPrice = document.querySelector('.totals .right span');
                totalPrice.innerHTML = (parseFloat(subTotal.innerHTML) + parseFloat(transportCost.innerHTML)).toFixed(2);
            }
            function getCartss(){
                let currentCart = window.localStorage.getItem('cart');
                if(currentCart){
                    console.log(currentCart);
                    window.livewire.emit('sgetCart', currentCart);
                }
            }
        @else
        @endif
        document.addEventListener("DOMContentLoaded", () => {
            window.livewire.on('changeCountry', (e) => {
                let selectedCountry = document.querySelector('.current-country');
                if(selectedCountry){
                    selectedCountry.querySelector('img').src = '/images/'+e+'.png';
                }
            });
        })
    </script>
</div>
