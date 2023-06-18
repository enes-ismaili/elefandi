<div class="allprodcucts">
    <div class="section-title">Të gjithë produktet</div>
    <div class="product-list flex">
        @php
            $isLoggedIn = false;
            if(current_user()){
                $isLoggedIn = current_user()->id;
            }
        @endphp
        @php
            // if($pageNumber == 3){
            //     dd($cproducts);
            // }
        @endphp
        @foreach($products as $product)
            @php
                if($product->minoffers() && $product->minoffers()->discount != 0){
                    $offer = $product->minoffers();
                    if($offer->type < 3){
                        $offerPrice = round($product->price - (($product->price * $offer->discount)/100), 2);
                        $offerDiscount = '-'.round($offer->discount, 1).'%';
                    } else {
                        $offerPrice = $offer->discount;
                        $offerDiscount = '-'.($product->price - $offer->discount).'€';
                    }
                } else {
                    $offer = '';
                }
                $productLink = route('single.product', [$product->owner->slug, $product->id]);
            @endphp
            <div class="product">
                <a href="{{ $productLink }}">
                    <div class="product_image">
                        @if($offer)
                            <div class="product-offer">{{ $offerDiscount }}</div>
                        @endif
                        <img src="{{ (file_exists('photos/products/230/'.$product->image)) ? asset('/photos/products/230/'.$product->image) : asset('/photos/products/'.$product->image) }}" alt="">
                    </div>
                </a>
                <div class="product_info">
                    <a href="{{ $productLink }}">
                        <div class="product_info_title">{{$product->name}}</div>
                    </a>
                    <div class="product_info_vendor">
                        <a href="{{ route('single.vendor', $product->owner->slug) }}">
                            {!! $product->owner->name !!}
                            @if($product->owner->verified)
                                <img title="Dyqan i verifikuar" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">
                            @endif
                        </a>
                    </div>
                    @if($product->ratings->count())
                    <div class="product_info_rating">
                        @php
                            if($product->ratings->count()){
                                $ratingAverage = $product->ratings->pluck('rating')->avg();
                                $ratingAverageRoundS = round(($ratingAverage*10)/5) * 5;
                                $ratingAverageRound = round($ratingAverage, 2);
                            } else {
                                $ratingAverageRoundS = 0;
                            }
                        @endphp
                        <div class="ratings r{{ $ratingAverageRoundS }}">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                    </div>
                    @endif
                    <div class="product_info_price">
                        @if($offer)
                            <div class="current_price">{{ number_format($offerPrice, 2) }}€</div>
                            <div class="old_price">{{ number_format($product->price, 2) }}€</div>
                        @else
                            <div class="current_price">{{ number_format($product->price, 2) }}€</div>
                        @endif
                    </div>
                    <livewire:products.add-to-cart :product="$product" :isLoggedIn="$isLoggedIn" :plink="$productLink" :wire:key="'l-'.$product->id">
                </div>
            </div>
        @endforeach
    </div>
    @if ($hasMorePages)
    <div class="load-more-products">
        <div class="loader">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    @endif
</div>