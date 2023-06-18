<div>
    <div class="mini-cart-icon icon" wire:click="showCart(1)">
        <div class="h-icon">
            <svg id="Group_56" data-name="Group 56" xmlns="http://www.w3.org/2000/svg" width="36.948" height="35.161" viewBox="0 0 36.948 35.161">
                <g id="Group_54" data-name="Group 54">
                    <g id="shopping-bag">
                        <g id="Group_2" data-name="Group 2" transform="translate(0)">
                            <path id="Path_3" data-name="Path 3" d="M45.3,32.862,42.785,9.4a.769.769,0,0,0-.755-.672H37.371V7.3a7.3,7.3,0,0,0-14.605,0V8.73H17.981a.769.769,0,0,0-.755.672L14.707,32.862a.752.752,0,0,0,.168.588.677.677,0,0,0,.546.252H44.589a.677.677,0,0,0,.546-.252A.752.752,0,0,0,45.3,32.862ZM36.615,12.969a.672.672,0,1,1-.672.672A.663.663,0,0,1,36.615,12.969ZM24.276,7.3a5.792,5.792,0,0,1,11.584,0V8.73H24.276Zm-.755,5.666a.672.672,0,1,1-.672.672A.663.663,0,0,1,23.521,12.969ZM16.3,32.149l2.35-21.95h4.113v1.385a2.14,2.14,0,1,0,1.427,0V10.2H35.776v1.385a2.14,2.14,0,1,0,1.427,0V10.2h4.281l2.35,21.95Z" transform="translate(-14.696)" />
                        </g>
                    </g>
                </g>
                <ellipse id="Ellipse_3" data-name="Ellipse 3" cx="8.941" cy="8.941" rx="8.941" ry="8.941" transform="translate(19.066 17.279)" fill="#ff1c1c" />
                <text data-name="3" transform="translate(28.136 29.939)" fill="#fff" font-size="11" id="cartCount">
                    <tspan x="-3.207" y="0">{{ $prodCount }}</tspan>
                </text>
            </svg>
        </div>
    </div>
    <div class="mini-carts @if($showCart) show @endif">
        <div class="bg_modal" wire:click.prevent="showCart(0)"></div>
        <div wire:loading>
            <div class="sk-circle">
                <div class="sk-circle1 sk-child"></div>
                <div class="sk-circle2 sk-child"></div>
                <div class="sk-circle3 sk-child"></div>
                <div class="sk-circle4 sk-child"></div>
                <div class="sk-circle5 sk-child"></div>
                <div class="sk-circle6 sk-child"></div>
                <div class="sk-circle7 sk-child"></div>
                <div class="sk-circle8 sk-child"></div>
                <div class="sk-circle9 sk-child"></div>
                <div class="sk-circle10 sk-child"></div>
                <div class="sk-circle11 sk-child"></div>
                <div class="sk-circle12 sk-child"></div>
            </div>
        </div>
        <div class="cart-info" wire:loading.remove>
            <div class="close-popup" wire:click.prevent="showCart(0)"></div>
            @if(count($products))
                @php
                    $totalPrice = 0;
                    $transPrice = 0;
                @endphp
                <div class="product-list">
                    @foreach($products as $product)
                        @php
                            if($getFromJson){
                                $currProduct = App\Models\Product::where('id', '=', $product->id)->first();
                            } else {
                                $currProduct = $product->product;
                            }
                        @endphp
                        @if($currProduct->status == 1 && $currProduct->vstatus == 1)
                        @php
                            $noStock = false;
                            $productVariant = 0;
                            if($product->variant_id){
                                $productVariant = $product->variant_id;
                            }
                            $currentVariant = $currProduct->cartVariant($productVariant);
                            if($currentVariant){
                                $prodPrice = $currentVariant->price ? $currentVariant->price : $currProduct->price;
                                $oldPrice = $prodPrice;
                                if($currProduct->offers($productVariant)){
                                    $offer = $currProduct->offers($productVariant);
                                    if($offer->type < 3){
                                        $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                                    } else {
                                        $prodPrice = $offer->discount;
                                    }
                                } else {
                                    $offer = '';
                                }
                                $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $currProduct->stock;
                                $productStock = $product->qty;
                                if($prodMaxStock){
                                    if($product->qty > $prodMaxStock){
                                        $productStock = $prodMaxStock;
                                    }
                                    if($product->qty < 1){
                                        $noStock = true;
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
                            $shippingCost = 0;
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
                                if($getShipping->transport >= 0){
                                    $shippingCTransport = true;
                                }
                            }
                            if(!$noStock && $shippingCTransport){
                                $totalPrice += ($product->qty * $prodPrice);
                                $transPrice += $shippingCost;
                            }
                            if(!$product->qty) {
                                $noStock = true;
                            }
                        @endphp
                        <div class="cart-product @if($noStock)no-stock @endif @if(!$shippingCTransport)no-shipping @endif">
                            <div class="thumbnail">
                                {{-- <img src="{{asset('photos/products/'.$currProduct->image)}}" alt=""> --}}
                                <img src="{{ (file_exists('photos/products/230/'.$currProduct->image)) ? asset('/photos/products/230/'.$currProduct->image) : asset('/photos/products/'.$currProduct->image) }}" alt="">
                            </div>
                            <div class="info">
                                @if($getFromJson)
                                    <div class="remove-cart" onClick="removeCart('p{{$product->id}}v{{$productVariant}}')"><i class="fas fa-times"></i></div>
                                @else
                                    <div class="remove" wire:click="removeProduct({{$product->id}})"><i class="fas fa-times"></i></div>
                                @endif
                                <div class="name">
                                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">{{$currProduct->name}}</a>
                                </div>
                                <div class="vendor">
                                    <a href="{{ route('single.vendor', $currProduct->owner->slug) }}">{!! $currProduct->owner->name !!}</a>
                                </div>
                                @php
                                    // $currentVariant = $currProduct->cartVariant($productVariant);
                                    // if($currentVariant){
                                    //     $prodPrice = $currentVariant->price ? $currentVariant->price : $currProduct->price;
                                    // } else {
                                    //     $prodPrice = $currProduct->price;
                                    // }
                                    // $totalPrice += ($product->qty * $prodPrice);
                                @endphp
                                
                                {{-- <div class="price">{{$product->qty}} x {{$prodPrice.'€'}}</div> --}}
                                @if($offer)
                                    <div class="price"><span>{{$productStock}} x {{number_format($prodPrice, 2).'€'}}</span></div>
                                @else
                                    <div class="price"><span>{{$productStock}} x {{number_format($prodPrice,2).'€'}}</span></div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                <div class="cart-footer">
                    <div class="tworows subtotal">
                        <div class="left">Nëntotali</div>
                        <div class="right">{{number_format($totalPrice,2).'€'}}</div>
                    </div>
                    <div class="tworows buttons">
                        <a href="{{ route('view.cart') }}" class="view-cart">Shiko Shportën</a>
                        <a href="{{ route('view.checkout') }}" class="checkout">@if(count($products) > 1) Bli Artikujt @else Bli Artikullin @endif</a>
                    </div>
                </div>
            @else
                <div class="no-cart">Ju nuk keni artikuj në shportë!</div>
            @endif
        </div>
    </div>
    <script>
        @if(!$isLogged)
            let currentCartt = window.localStorage.getItem('cart');
            if(currentCartt){
                newcurrentCartt = JSON.parse(currentCartt);
                let propSymb = Object.keys(newcurrentCartt);
                document.querySelector('#cartCount').innerHTML  = '<tspan x="-3.207" y="0">'+propSymb.length+'</tspan>';
            }
            document.addEventListener("DOMContentLoaded", () => {
                window.livewire.on('getCart', () => {
                    getCart()
                    let removeCarts = document.querySelectorAll('.remove-cart')
                    removeCarts.forEach(removeCart => {
                        removeCart.addEventListener('click', e=>{
                            // console.log(e);
                        })
                    })
                });
            });
            function getCart(){
                let currentCart = window.localStorage.getItem('cart');
                if(currentCart){
                    newCart = JSON.parse(currentCart);
                    window.livewire.emit('getCarts', currentCart);
                }
            }
            function removeCart(id){
                let currentCartOld = window.localStorage.getItem('cart');
                if(currentCartOld){
                    newCart = JSON.parse(currentCartOld);
                    delete newCart[id];
                    window.localStorage.setItem('cart', JSON.stringify(newCart));
                    let currentCartS = window.localStorage.getItem('cart');
                    window.livewire.emit('getCarts', currentCartS);
                    window.livewire.emitTo('products.product-price', 'updatedCart');
                }
            }
        @endif
    </script>
</div>
