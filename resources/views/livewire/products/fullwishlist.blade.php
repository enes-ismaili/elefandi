<div class="all-wishlist-products">
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
    <div class="wishlist-products" wire:loading.remove>
        @if($getFromJson)
            @foreach($wishlistss as $wishlist)
                @php
                    if(is_array($wishlist)){
                        $wishListId = $wishlist['id'];
                    } else {
                        $wishListId = $wishlist->id;
                    }
                    $currProduct = App\Models\Product::where('id', '=', $wishListId)->first();
                @endphp
                <div class="wishlist-product">
                    <div class="remove-wishlist" onClick="removeWishFull('p{{$currProduct->id}}')"><i class="fas fa-times"></i></div>
                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">
                        <div class="thumbnail">
                            <img src="{{ asset('photos/products/'.$currProduct->image) }}" alt="">
                        </div>
                        <div class="name">{{ $currProduct->name }}</div>
                    </a>
                </div>
            @endforeach
        @else
            @foreach($wishlistss as $wishlist)
                <div class="wishlist-product">
                    <div class="remove-wishlist" wire:click="removeWishlist('{{ $wishlist->id }}')"><i class="fas fa-times"></i></div>
                    <a href="{{ route('single.product', [$wishlist->product->owner->slug, $wishlist->product_id]) }}">
                        <div class="thumbnail">
                            <img src="{{ asset('photos/products/'.$wishlist->product->image) }}" alt="">
                        </div>
                        <div class="name">{{ $wishlist->product->name }}</div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
    @if(!$isLoggedIn)
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                getWishFull()
                window.livewire.on('getWishFu', () => {
                    console.log("DOMContentLoaded")
                    getWishFull()
                })
            })
            function getWishFull(){
                let currentFWish = window.localStorage.getItem('wishlist');
                if(currentFWish){
                    console.log(currentFWish)
                    window.livewire.emitTo('products.fullwishlist', 'getWishlistsFulls', currentFWish);
                }
            }
            function removeWishFull(id){
                let currentWishFOld = window.localStorage.getItem('wishlist');
                if(currentWishFOld){
                    let newWish = JSON.parse(currentWishFOld);
                    delete newWish[id];
                    window.localStorage.setItem('wishlist', JSON.stringify(newWish));
                    let currentCartS = window.localStorage.getItem('wishlist');
                    window.livewire.emit('getWishlistsFulls', currentCartS);
                    console.log('remove')
                    window.livewire.emitTo('header.wishlist', 'getWishlistsUpdate', currentCartS);
                }
            }
        </script>
    @endif
</div>
