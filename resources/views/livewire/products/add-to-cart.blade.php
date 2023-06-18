<div class="product_cart">
    @if($isLoggedIn)
        @if($hasVariants)
            <div class="add-cart">
                <a href="{{ $plink }}"><i class="fas fa-shopping-cart"></i>{{$addCartText}}</a>
            </div>
        @else
            <div class="add-cart" wire:click="addToCart">
                <i class="fas fa-shopping-cart"></i>{{$addCartText}}
            </div>
        @endif
        <div class="production_options">
            <div class="add-wishlist {{ ($isInWish)?'remove':'' }}" wire:click="addToWish"><i class="far fa-heart"></i></div>
        </div>
    @else
        @if($hasVariants)
            <div class="add-cart">
                <a href="{{ $plink }}"><i class="fas fa-shopping-cart"></i>{{$addCartText}}</a>
            </div>
        @else
            <div class="add-cart jadd" data-id="{{ $product->id }}" data-variant="0">
                <i class="fas fa-shopping-cart"></i>{{$addCartText}}
            </div>
        @endif
        <div class="production_options">
            <div class="add-wishlist jadd" data-id="{{ $product->id }}"><i class="far fa-heart"></i></div>
        </div>
    @endif
</div>
