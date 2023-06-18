<div>
    <div class="product-price">
        @if($noVariant)
            @php $this->dispatchBrowserEvent('initCountDown', 'stop'); @endphp
            <div class="product-price-show"><div class="price"><span>Ky variant nuk është në stok</span></div></div>
        @else
            @if($product->offers($productVariant) && $product->offers($productVariant)->discount != 0)
                @php
                    $offer = $product->offers($productVariant);
                    if($offer->type < 3){
                        $offerPrice = round($productPrice - (($productPrice * $offer->discount)/100), 2);
                        $offerDiscount = '<b>-'.$offer->discount.'%</b><br>Zbritje';
                    } else {
                        $offerPrice = $offer->discount;
                        $offerDiscount = '<b>-'.($productPrice-$offer->discount).'€</b><br>Zbritje';
                    }
                    if($offer->main){
                        $offerExpire = \Carbon\Carbon::parse($offer->main->expire_date)->format('U');
                        $this->dispatchBrowserEvent('initCountDown', $offerExpire.'000');
                    }
                @endphp
                <div class="product-price-show">
                    <div class="price">
                        <span class="current_price">{{ number_format($offerPrice, 2) }}€</span>
                        <span class="old_price">{{ number_format($productPrice, 2) }}€</span>
                    </div>
                    <div class="offer-disscount">{!! $offerDiscount !!}</div>
                </div>
            @else
                @php $this->dispatchBrowserEvent('initCountDown', 'stop'); @endphp
                <div class="product-price-show"><div class="price"><span class="current_price">{{ number_format($productPrice, 2) }}€</span></div></div>
            @endif
        @endif
        <div class="container-countdown1"></div>
    </div>
    <div class="divider c1"></div>
    @if($product->colors && $product->colors != '[]')
    <div class="colors">Ngjyrat: 
        <div class="variants">
            @foreach(json_decode($product->colors) as $color)
                <span id="{{$color->id}}" @if($selectedColors == $color->id) class="variant-option active" @else class="variant-option" @endif wire:click="changevariant('color', {{$color->id}})">{{$color->name}}</span>
            @endforeach
        </div>
    </div>
    @endif
    @if($product->attributes && $product->attributes != '[]')
        @if(count(json_decode($product->attributes))>0)
            <div class="variants">
                @foreach(json_decode($product->attributes) as $attribute)
                    <div class="attributes">{{$attribute->name}}: 
                        @foreach($attribute->options as $option)
                            <span id="{{$option}}" @if($selectedAttributes[$attribute->id] == $option) class="variant-option active" @else class="variant-option" @endif  wire:click="changevariant({{$attribute->id}}, '{{$option}}')">{{$option}}</span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    @endif
    <div class="stock">Sasia:
        <div class="stock-options">
            <div class="stock-btn remove">-</div>
            <div class="stock-show"><input type="number" name="tes" min="1" max="{{$maxStock}}" value="1" wire:model.defer="selectStock"></div>
            <div class="stock-btn add">+</div>
        </div>
        <span class="stock-info">(Gjithsej {{$maxStock}} artikuj në stok)</span>
    </div>
    <div class="product-transport">
        <div class="transport">Transporti: <span></span></div>
        <div class="tansport-countries">
            @foreach($shippingCountry as $country)
                <span id="c{{$country->id}}" class="country product-shipping">{{$country->name}}</span>
            @endforeach
        </div>
        <div class="delivery-time">Koha e arritjes se produktit: <span></span></div>
    </div>
    @if($product->personalize && $product->personalize != 'null')
    <div class="divider c1"></div>
    <div class="product-personalize">
        <div class="title">Shto personalizimin tuaj</div>
        <div class="form-group">
            <label for="personalize">{{ $product->personalize }}</label>
            <input type="text" class="form-control" id="personalize" placeholder="Shkruaj personalizimin për këtë produkt" wire:model.defer="personalizeProduct">
        </div>
    </div>
    @endif
    <div class="divider c1"></div>
    <div class="product-buttons">
        @if(!$noVariant)
			<div class="more-error">{{ $buttonsError }}</div>
            <div class="more">
                @if($isLogged)
                    @if($inWish)
                        <div class="remove-wishlist" wire:click="addToWishlist(2)"><i class="far fa-heart"></i></div>
                    @else
                        <div class="add-wishlist" wire:click="addToWishlist(1)"><i class="far fa-heart"></i></div>
                    @endif

                    @if($inCart)
                        <a href="{{ route('view.cart') }}" class="view-cart"><i class="fas fa-cart-plus"></i>Është në Shportë</a>
                    @else
                        <div class="add-cart" wire:click="addToCart"><i class="fas fa-cart-plus"></i>Shto në Shportë</div>
                    @endif
                @else
                    <div class="jadd-wishlist" data-id="{{ $product->id }}" data-variant="{{ $currentVariants }}"><i class="far fa-heart"></i></div>
                    <div class="jadd-cart" data-id="{{ $product->id }}" data-variant="{{ $currentVariants }}"><i class="fas fa-cart-plus"></i>Shto në Shportë</div>
                @endif
                <div class="buy-now" wire:click="openCheckout"><i class="fas fa-cart-plus"></i>Bli me një Klik</div>
                <livewire:products.buy-directly :product="$product">
            </div>
        @endif
    </div>

    
    <script>
        let addStock = document.querySelector('.stock-btn.add');
        let removeStock = document.querySelector('.stock-btn.remove');
        let inputStock = document.querySelector('.stock-show input');
        let productShippings = document.querySelectorAll('.product-shipping');
        // let shippingCountryCost = {{!! json_decode($productShippings) !!}};
        let shippingCountryCost = JSON.parse('{{!! $productShippings !!}}');
        let transCostG = 0;
        let transTimeG = '';

        @if(isset($offerExpire) && $offerExpire)
            let countdownTimer = document.querySelector('.container-countdown1')
            const eventAwesome = new CustomEvent('initCountDown', {
                bubbles: true,
                detail: {{ $offerExpire.'000' }}
            });
            window.dispatchEvent(eventAwesome)
        @endif

        productShippings.forEach(productShipping => {
            productShipping.addEventListener('click', (shipping)=>{
                document.querySelectorAll('.product-shipping.active').forEach(shippingActive => {
                    shippingActive.classList.remove('active');
                });
                shipping.target.classList.add('active');
                let currentShipping = shippingCountryCost[shipping.target.id];
                transCostG = currentShipping.cost+'€';
                if(currentShipping.cost == 0 || currentShipping.free == 1){
                    transCostG = 'Falas';
                }
                transTimeG = currentShipping.timeName;
                document.querySelector('.product-transport .transport span').innerHTML = transCostG;
                document.querySelector('.product-transport .delivery-time span').innerHTML = transTimeG;
            })
        })
        document.addEventListener("DOMContentLoaded", () => {
            changeTransport()
            window.livewire.on('changeVariant', () => {
                changeTransport()
            });
            window.livewire.on('updateCart', () => {
                changeTransport()
            });
            window.livewire.on('updateCarts', () => {
                changeTransport()
            });
            window.livewire.on('updateWish', () => {
                changeTransport()
            });
        });
        @if(!$isLogged)
        let addCart = document.querySelector('.jadd-cart');
        let addWishlist = document.querySelector('.jadd-wishlist');
        let currentCart = window.localStorage.getItem('cart');
        let currentWishlist = window.localStorage.getItem('wishlist');
        
        document.addEventListener("DOMContentLoaded", () => {
            changeTransport()
            window.livewire.on('changeVariant', () => {
                changeTransport()
                getCartWish()
            });
            window.livewire.on('updateCart', () => {
                changeTransport()
                getCartWish()
            });
            window.livewire.on('updateCarts', () => {
                changeTransport()
                getCartWish()
            });
        });
        function getCartWish(){
            currentCart = window.localStorage.getItem('cart');
            let prodid = addCart.dataset.id;
            let variant = addCart.dataset.variant;
            let newCart = {};
            if(currentCart){
                newCart = JSON.parse(currentCart);
            }
            if(newCart['p'+prodid+'v'+variant]){
                addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Është në Shportë';
                addCart.classList.add('remove');
            } else {
                addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Shto në Shportë';
            }
            if(addWishlist){
                let newWishlist = {};
                if(currentWishlist){
                    newWishlist = JSON.parse(currentWishlist);
                }
                if(newWishlist['p'+prodid+'v'+variant]){
                    addWishlist.classList.add('remove');
                }
            }
        }
        if(addWishlist){
            let prodid = addCart.dataset.id;
            let variant = addCart.dataset.variant;
            let newWishlist = {};
            if(currentWishlist){
                newWishlist = JSON.parse(currentWishlist);
            }
            if(newWishlist['p'+prodid]){
                addWishlist.classList.add('remove');
            }
            addWishlist.addEventListener('click', (addwishlist)=> {
                prodid = addCart.dataset.id;
                variant = addCart.dataset.variant;
                if(addWishlist.classList.contains('remove')){
                    if(currentWishlist){
                        delete newWishlist['p'+prodid];
                        window.localStorage.setItem('wishlist', JSON.stringify(newWishlist));
                        addWishlist.classList.remove('remove');
                    }
                } else {
                    let newElem = {id:prodid};
                    newWishlist['p'+prodid] = newElem;
                    window.localStorage.setItem('wishlist', JSON.stringify(newWishlist));
                    addWishlist.classList.add('remove');
                }
            });
        }
        if(addCart){
            let prodid = addCart.dataset.id;
            let variant = addCart.dataset.variant;
            let newCart = {};
            if(currentCart){
                newCart = JSON.parse(currentCart);
            }
            if(newCart['p'+prodid+'v'+variant]){
                addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Është në Shportë';
                addCart.classList.add('remove');
            } else {
                addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Shto në Shportë';
            }
            addCart.addEventListener('click', (addcart)=> {
                prodid = addCart.dataset.id;
                variant = addCart.dataset.variant;
                let personalize = document.querySelector('.product-personalize #personalize');
                let personalizeVal = '';
                if(personalize){
                    personalizeVal = personalize.value;
                }
                if(addCart.classList.contains('remove')){
                    if(currentCart){
                        delete newCart['p'+prodid+'v'+variant];
                        window.localStorage.setItem('cart', JSON.stringify(newCart));
                        addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Shto në Shportë';
                        addCart.classList.remove('remove');
                    }
                } else {
                    let moreErrorButton = document.querySelector('.product-buttons .more-error');
                    if(inputStock.value > 0){
                        let newElem = {id:prodid, variant_id:variant, qty: inputStock.value, personalize: personalizeVal};
                        newCart['p'+prodid+'v'+variant] = newElem;
                        window.localStorage.setItem('cart', JSON.stringify(newCart));
                        addCart.innerHTML = '<i class="fas fa-cart-plus"></i>Është në Shportë';
                        addCart.classList.add('remove');
                        moreErrorButton.innerHTML = '';
                    } else {
                        moreErrorButton.innerHTML = 'Ky produkt nuk mund të blihet me një klik pasi nuk keni zgjedhur Sasinë'
                    }
                }
                let propSymb = Object.keys(newCart);
                console.log(document.querySelector('#cartCount'));
                document.querySelector('#cartCount').innerHTML  = '<tspan x="-3.207" y="0">'+propSymb.length+'</tspan>';
            })
        }
        @endif
        function changeTransport(){
            if(window.getCookie('country_id')){
                currentLocationP = window.getCookie('country_id');
            } else {
                window.setCookie('country_id', 1)
                currentLocationP = 1;
            }
            let currentShippingP = shippingCountryCost['c'+currentLocationP];
            document.querySelectorAll('.product-shipping.active').forEach(shippingActive => {
                shippingActive.classList.remove('active');
            });
            document.querySelector('#c'+currentLocationP).classList.add('active');
            transCostG = currentShippingP.cost+'€';
            if(currentShippingP.free ==1 || currentShippingP.cost == 0){
                transCostG = 'Falas';
            }
            transTimeG = currentShippingP.timeName;
            document.querySelector('.product-transport .transport span').innerHTML = transCostG;
            document.querySelector('.product-transport .delivery-time span').innerHTML = transTimeG;
        }
        if(inputStock){
            if(addStock){
                addStock.addEventListener('click', (addstock)=> {
                    changeStock(1);
                })
            }
            if(removeStock){
                removeStock.addEventListener('click', (removestock)=> {
                    changeStock(2);
                })
            }
        }
        function changeStock(e){
            // document.querySelector('.stock-show input').value = parseInt(inputStock.value) + 1;
            // var event = new Event('change');
            // inputStock.dispatchEvent(event, { bubbles: true });
            // var event1 = new Event('focus');
            // inputStock.dispatchEvent(event1, { bubbles: true });
            // var event2 = new Event('toggle');
            // inputStock.dispatchEvent(event2, { bubbles: true });
            inputStock.dispatchEvent(new Event('input'));
            if(e==1){
                if(parseInt(inputStock.value) < parseInt(inputStock.max)) {
                    inputStock.value = parseInt(inputStock.value) + 1;
                    // @this.set('selectStock', newInput);
                    // @this.entangle('selectStock').defer
                }
            } else {
                if(parseInt(inputStock.value) > parseInt(inputStock.min)) {
                    inputStock.value = parseInt(inputStock.value) - 1;
                    // @this.set('selectStock', newInput)
                }
            }
            
            // console.log(inputStock.max);
        }
    </script>
</div>
