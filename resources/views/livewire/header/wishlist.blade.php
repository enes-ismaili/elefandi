<div>
    <div class="wishlist-icon icon"  wire:click="showWishlist">
        <div class="h-icon">
            <i class="far fa-heart"></i>
            <span class="notify-icon">{{ $wishlistsC }}</span>
        </div>
        <span class="dmobile icon-title">Lista e Dëshirave</span>
    </div>
    <div class="mini-wishlist @if($showWish) show @endif">
        <div class="bg_modal" wire:click.prevent="hideWishlist"></div>
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
        <div class="wishlist-info" wire:loading.remove>
            <div class="close-popup" wire:click.prevent="hideWishlist"></div>
            @if($wishlistsC) 
                <div class="wishlist-info-products">
                    @if($getFromJson)
                        @foreach($wishlists as $wishlist)
                            @php
                                if(is_array($wishlist)){
                                    $wishListId = $wishlist['id'];
                                } else {
                                    $wishListId = $wishlist->id;
                                }
                                $currProduct = App\Models\Product::where('id', '=', $wishListId)->first();
                            @endphp
                            <div class="wishlist-product">
                                <div class="remove-wishlist" onClick="removeWish('p{{$currProduct->id}}')"><i class="fas fa-times"></i></div>
                                {{-- <div class="remove-wishlist" wire:click="removeWishlist('{{ $wishlist->id }}')"><i class="fas fa-times"></i></div> --}}
                                <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">
                                    <div class="thumbnail">
                                        {{-- <img src="{{ asset('photos/products/'.$currProduct->image) }}" alt=""> --}}
                                        <img src="{{ (file_exists('photos/products/230/'.$currProduct->image)) ? asset('/photos/products/230/'.$currProduct->image) : asset('/photos/products/'.$currProduct->image) }}" alt="">
                                    </div>
                                    <div class="name">{{ $currProduct->name }}</div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        @foreach($wishlists as $wishlist)
                            <div class="wishlist-product">
                                <div class="remove-wishlist" wire:click="removeWishlist('{{ $wishlist->id }}')"><i class="fas fa-times"></i></div>
                                <a href="{{ route('single.product', [$wishlist->product->owner->slug, $wishlist->product_id]) }}">
                                    <div class="thumbnail">
                                        {{-- <img src="{{ asset('photos/products/'.$wishlist->product->image) }}" alt=""> --}}
                                        <img src="{{ (file_exists('photos/products/230/'.$wishlist->product->image)) ? asset('/photos/products/230/'.$wishlist->product->image) : asset('/photos/products/'.$wishlist->product->image) }}" alt="">
                                    </div>
                                    <div class="name">{{ $wishlist->product->name }}</div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                @if($wishlistsC > 6)
                    <div class="wishlist-more tcenter"><a href="{{ route('view.wishlist') }}">Shiko të gjitha</a></div>
                @endif
            @else
                <div class="no-cart tcenter">Ju nuk keni asnjë produkt në listën e dëshirave</div>
            @endif
        </div>
    </div>
    @if(!$isLoggedIn)
        <script>
            let currentWishList = window.localStorage.getItem('wishlist');
            if(currentWishList){
                let newcurrentWishlist = JSON.parse(currentWishList);
                let propSymb = Object.keys(newcurrentWishlist);
                document.querySelector('.wishlist-icon .notify-icon').innerHTML  = propSymb.length;
            }
            document.addEventListener("DOMContentLoaded", () => {
                window.livewire.on('getWish', () => {
                    getWish()
                })
            })
            function getWish(){
                let currentWish = window.localStorage.getItem('wishlist');
                if(currentWish){
                    window.livewire.emit('getWishlists', currentWish);
                }
            }
            function removeWish(id){
                let currentWishOld = window.localStorage.getItem('wishlist');
                if(currentWishOld){
                    let newWish = JSON.parse(currentWishOld);
                    delete newWish[id];
                    window.localStorage.setItem('wishlist', JSON.stringify(newWish));
                    let currentCartS = window.localStorage.getItem('wishlist');
                    window.livewire.emit('getWishlists', currentCartS);
                    window.livewire.emitTo('products.product-price', 'updatedCart');
                }
            }
        </script>
    @endif
</div>
