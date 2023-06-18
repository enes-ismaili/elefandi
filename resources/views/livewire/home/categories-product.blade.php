<div>
    <div class="section-title">
        {{ $category->name }}
        <div class="right-cat">
            <div class="select-cat {{ ($currentCategoryId == $category->id) ? 'active' : '' }}" wire:click="selectCategory('{{ $category->id }}')">{{ $category->name }}</div>
            @foreach($categoryChild as $ccategory)
                <div class="select-cat {{ ($currentCategoryId == $ccategory->id) ? 'active' : '' }}" wire:click="selectCategory('{{ $ccategory->id }}')">{{ $ccategory->name }}</div>
            @endforeach
        </div>
    </div>
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
    <div class="section-content" wire:loading.remove>
        <div class="left">
            @if(count($sliderss))
                <div class="category-slider swiper-container" id="catSlider-{{ $category->id }}">
                    <div class="swiper-wrapper">
                        @foreach($sliderss as $slider)
                            <div class="swiper-slide" data-hash="slide{{ $loop->index }}">
                                @if($slider->link)
                                    <a href="{{ $slider->link }}">
                                        <img src="{{ asset('photos/category/'.$slider->image) }}" alt="">
                                    </a>
                                @else
                                    <img src="{{ asset('photos/category/'.$slider->image) }}" alt="">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination{{ $category->id }}"></div>
                </div>
            @endif
            <div class="category-products">
                <div class="order-select">
                    <div class="select-cat {{ ($orderList == 1) ? 'active' : '' }}" wire:click="selectOrder(1)">Produktet e fundit</div>
                    <div class="select-cat {{ ($orderList == 2) ? 'active' : '' }}" wire:click="selectOrder(2)">Ne zbritje</div>
                </div>
                <div class="product-list flex">
                    @php
                        $isLoggedIn = false;
                        if(current_user()){
                            $isLoggedIn = current_user()->id;
                        }
                    @endphp
                    @if($productList->count())
                    @foreach ($productList as $product)
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
                                    {{-- <img src="{{asset('photos/products/'.$product->image)}}" alt=""> --}}
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
                                <livewire:products.add-to-cart :product="$product" :isLoggedIn="$isLoggedIn" :plink="$productLink" :wire:key="'c-'.$currentCategoryId.$product->id">
                            </div>
                        </div>
                    @endforeach
                    @else
                        <div class="no-products">Nuk është gjetur asnje produkt</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="right">
            <div class="right-list">
                <div class="title">Ne ju sugjerojmë</div>
                <div class="product-list">
                    @foreach($suggestions as $product)
                        @php
                            if($product->minoffers()){
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
                        @endphp
                        <div class="product">
                            <a href="{{route('single.product', [$product->owner->slug, $product->id])}}">
                                <div class="product_image">
                                    @if($offer)
                                        <div class="product-offer">{{ $offerDiscount }}</div>
                                    @endif
                                    {{-- <img src="{{asset('photos/products/'.$product->image)}}" alt=""> --}}
                                    <img src="{{ (file_exists('photos/products/70/'.$product->image)) ? asset('/photos/products/70/'.$product->image) : asset('/photos/products/'.$product->image) }}" alt="">
                                </div>
                            </a>
                            <div class="product_info">
                                <a href="{{route('single.product', [$product->owner->slug, $product->id])}}">
                                    <div class="product_info_title">{{$product->name}}</div>
                                </a>
                                <div class="product_info_price">
                                    @if($offer)
                                        <div class="current_price">{{ number_format($offerPrice, 2) }}€</div>
                                        <div class="old_price">{{ number_format($product->price, 2) }}€</div>
                                    @else
                                        <div class="current_price">{{ number_format($product->price, 2) }}€</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        import Swiper from '{{ asset('js/swiper.min.js') }}'
        let catSliderOption = {
            slidesPerView: 1,
            pagination: {
                el: '.swiper-pagination{{ $category->id }}',
                type: 'bullets',
            },
        }
        let thisSLider = document.querySelector('#catSlider-{{ $category->id }}');
        if(window.catswiper['p{{ $category->id }}']){
            window.catswiper['p{{ $category->id }}'].destroy();
        }
        window.catswiper['p{{ $category->id }}'] = new Swiper(thisSLider, catSliderOption);
        window.catswiper['p{{ $category->id }}'].init();
    </script>
</div>
