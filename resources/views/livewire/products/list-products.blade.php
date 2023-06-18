<div>
    <script>
        let inputPrices = document.querySelectorAll('.slider-price');
        inputPrices.forEach(inputS => {
            inputS.addEventListener('input', e => {
            let _t = e.target;
            _t.parentNode.style.setProperty(`--${_t.id}`, +_t.value)
            }, false);
        })
    </script>
    <div class="product-listing">
        <div class="left taxonomy-filter">
            <div class="card">
                <div class="filter-vendor">
                    <div class="title">Sipas Dyqanit</div>
                    <ul>
                        @foreach($vendors as $vendor)
                            <li>
                                <div class="form-group">
                                    <input class="form-control" value="{{$vendor->id}}" type="checkbox" id="{{'v'.$vendor->id}}" wire:model="selectedVendor">
                                    <label for="{{'v'.$vendor->id}}">{!! $vendor->name !!}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="divider"></div>
                <div class="filter-price">
                    <div class="title">Sipas Çmimit</div>
                    <div class="wrap" role="group" aria-labelledby="multi-lbl" style="--a: {{$minSlider}}; --b: {{$maxSlider}}; --min: {{$minSelSlider}}; --max: {{$maxSelSlider}};">
                        <label class="sr-only" for="a">Value A:</label>
                        <input id="a" type="range" class="slider-price" wire:model.lazy="minSlider" min="{{$minSelSlider}}" max="{{$maxSelSlider}}">
                        <output for="a" style="--c: var(--a)"></output>
                        <label class="sr-only" for="b">Value B:</label>
                        <input id="b" type="range" class="slider-price" wire:model.lazy="maxSlider" min="{{$minSelSlider}}" max="{{$maxSelSlider}}">
                        <output for="b" style="--c: var(--b)"></output>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="shopping-title">{!! $pageIcon !!}{{$pageName}}</div>
            <div class="shopping-list">
                <div class="filter-menu"><i class="fas fa-filter"></i></div>
                <div class="shopping-header">
                    <p>Jane gjetur <strong>{{$products->total()}}</strong> produkte</p>
                    <div class="shopping-actions">
                        <div class="order-filter">
                            <select id="order-filter" wire:model="orderFilter">
                                <option value="1">Shfaq produktet e fundit</option>
                                <option value="2">Shfaq vetem produktet në zbritje</option>
                                <option value="3">Rëndit sipas çmimit më të lartë</option>
                                <option value="4">Rëndit sipas çmimit më të ulët</option>
                            </select>
                        </div>
                        <div class="product-listing-view">
                            <span>Pamja: </span>
                            <span class="link @if($listView == 1) active c1 @endif" wire:click="changeView(1)"><i class="icon-th-large"></i></span>
                            <span class="link @if($listView == 2) active c1 @endif" wire:click="changeView(2)"><i class="icon-th-list"></i></span>
                        </div>
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
                @if(count($products))
                <div class="product-list flex @if($listView == 2) v3 @else v2 @endif"  wire:loading.remove>
                    @php
                        $isLoggedIn = false;
                        if(current_user()){
                            $isLoggedIn = current_user()->id;
                        }
                    @endphp
                    @foreach($products as $product)
                        @php
                            if($this->type == 5){
                                $product = $product->product;
                            }
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
                        <div class="product {{ $cNu }}">
                            <a href="{{ $productLink }}">
                                <div class="product_image">
                                    @if($offer)
                                        <div class="product-offer">{{ $offerDiscount }}</div>
                                    @endif
                                    {{-- <img src="{{asset('/photos/products/'.$product->image)}}" alt=""> --}}
                                    <img src="{{ (file_exists('photos/products/230/'.$product->image)) ? asset('/photos/products/230/'.$product->image) : asset('/photos/products/'.$product->image) }}" alt="">
                                </div>
                            </a>
                            <div class="product_info">
                                <div class="prod-col">
                                    <a href="{{ $productLink }}">
                                        <div class="product_info_title">{{$product->name}}</div>
                                    </a>
                                    <div class="product_info_vendor">
                                        <a href="{{ route('single.vendor', $product->owner->slug) }}">
                                            {!! $product->owner->name !!}
                                            @if($product->owner->verified)
                                                <img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">
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
                                </div>
                                <div class="prod-col">
                                    <div class="product_info_price">
                                        @if($offer)
                                            <div class="current_price">{{ number_format($offerPrice, 2) }}€</div>
                                            <div class="old_price">{{ number_format($product->price, 2) }}€</div>
                                        @else
                                            <div class="current_price">{{ number_format($product->price, 2) }}€</div>
                                        @endif
                                    </div>
                                    <livewire:products.add-to-cart :product="$product" :isLoggedIn="$isLoggedIn" :plink="$productLink" :wire:key="'ls-'.$product->id">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{ $products->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
