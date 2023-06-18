<x-app-layout>
    @push('scripts')
        <script src="{{ asset('js/zuck.js') }}"></script>
        <script>
            let timestamp = function() {
                let timeIndex = 0;
                let shifts = [35, 60, 60 * 3, 60 * 60 * 2, 60 * 60 * 25, 60 * 60 * 24 * 4, 60 * 60 * 24 * 10];
                let now = new Date();
                let shift = shifts[timeIndex++] || 0;
                let date = new Date(now - shift * 1000);
                return date.getTime() / 1000;
            };
            let stories = new Zuck('stories', {
                backNative: true,
                previousTap: true,
                skin: "Snapgram",
                autoFullScreen: false,
                avatars: true,
                paginationArrows: false,
                list: false,
                cubeEffect: true,
                localStorage: true,
                stories: [
                    @foreach($stories as $story)
                        @php
                            $storyImage = $story->items()->where('cactive', '=', '1')->where('type',1)->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime)->first();
                            if($storyImage){
                                $storyImage = asset('/photos/story/'.$storyImage->image);
                            } else {
                                $storyImage = '';
                            }
                        @endphp
                        Zuck.buildTimelineItem(
                            "ST{{ $story->id }}", 
                            "{{ $storyImage }}",
                            "{{ $story->name }}",
                            "#",
                            timestamp(),
                            [
                                @foreach($story->items->where('cactive', '=', '1')->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime) as $item)
                                    ["{{$item->id}}", "{{ ($item->type==2)?'video':'photo' }}", "{{$item->length}}", "{{ route('single.image', $item->image) }}", "{{asset('photos/story/'.$item->image)}}", "{{ route('story.link',$item->id ) }}", "{{$item->name}}", false, "{{ strtotime($item->updated_at) }}"],
                                @endforeach
                            ]
                        ),
                    @endforeach
                ],
            });
        </script>
        <script type="module">
            import Swiper from '{{ asset('js/swiper.min.js') }}';
            const swiperStories = new Swiper('.mySwiperStories', {
                slidesPerView: 11,
                slidesPerGroup: 4,
                spaceBetween: 18,
				slideClass: 'story',
                navigation: {
                    nextEl: '.top-stories .right',
                    prevEl: '.top-stories .left',
                },
                scrollbar: {
                    el: '.swiper-scrollbar',
                },
                breakpoints: {
                    100: {
                        slidesPerView: 2,
						slidesPerGroup: 2,
                    },
                    500: {
                        slidesPerView: 4,
                    },
                    768: {
                        slidesPerView: 7,
                    },
                    1024: {
                        slidesPerView: 11,
                    }
                },
            })

            let catSwiperOption = {
              slidesPerView: 8,
              spaceBetween: 50,
              navigation: {
                nextEl: '.swipers-button-next',
                prevEl: '.swipers-button-prev',
              },
              pagination: false,
              breakpoints: {
                205: {
                  slidesPerView: 2,
                  spaceBetween: 20,
                },
                600: {
                  slidesPerView: 4,
                  spaceBetween: 20,
                },
                768: {
                  slidesPerView: 5,
                  spaceBetween: 40,
                },
                1024: {
                  slidesPerView: 8,
                  spaceBetween: 50,
                },
              },
            }
            let catSliderOption = {
              slidesPerView: 'auto',
              touchEventsTarget: 'wrapper',
              observer: true,
              pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            }
            
            let catSwiper = new Swiper(".categoryTSwiper", catSwiperOption);
            // let catSliders = document.querySelectorAll('.category-slider');
            // let rcatSlider = [];
            // catSliders.forEach(catSlider=>{
            //     rcatSlider[catSlider.id] = new Swiper(catSlider, catSliderOption);
            // })
            window.addEventListener('reinitSlider', event => {
                catSwiper.destroy();
                catSwiper = new Swiper(".categoryTSwiper", catSwiperOption);
                catSwiper.init();
                let goToSlide = 0;
                if(event.detail && event.detail > 0){
                    goToSlide = event.detail;
                }
                catSwiper.slideTo(goToSlide, 100)
            });
            window.addEventListener('reinitcSlider', event => {
                let cid = event.detail.cid;
                window['catswiper']['p'+cid].destroy();
                window['catswiper']['p'+cid] = new Swiper('#catSlider-'+event.detail.cid, {
                    slidesPerView: 1,
                    pagination: {
                        el: '.swiper-pagination'+cid,
                        type: 'bullets',
                    },
                });
                // window['catswiper']['p'+cid].destroy();
                // window['catswiper']['p'+cid].update();
            });
            document.addEventListener('DOMContentLoaded', function() {
                swiperStories.init();
                setTimeout(function(){ swiperStories.init();}, 3000);
            }, false);
        </script>
        <script>
            // const quotesEl = document.querySelector('.quotes');
            document.addEventListener('DOMContentLoaded', function() {
                const ploadMore = document.querySelector('.load-more-products');
                if(ploadMore){
                    let phasMorePages = true;
                    let pcurrentPage = 1;
                    let pLoading = false;
                    window.addEventListener('scroll', () => {
                        const {
                            scrollTop,
                            scrollHeight,
                            clientHeight
                        } = document.documentElement;
    
                        // console.log(scrollTop, scrollHeight, clientHeight);
                        if (!pLoading && phasMorePages && scrollTop + clientHeight >= scrollHeight - 400) {
                            pcurrentPage++;
                            //loadQuotes(pcurrentPage);
                            showLoader();
                            console.log('load More');
                            window.livewire.emitTo('home-products', 'loadMore');
                            setTimeout(() => {
                                hideLoader();
                            }, 800);
                        }
                    }, {
                        passive: true
                    });
    
                    const showLoader = () => {
                        pLoading = true;
                        ploadMore.classList.add('show');
                    };
                    const hideLoader = () => {
                        pLoading = false;
                        ploadMore.classList.remove('show');
                    };
                    const loadQuotes = async (page) => {
                        showLoader();
                        console.log('load More');
                    }
                }
            });
        </script>
    @endpush
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ mix('css/home.css') }}">
	 <style>
	 .top-stories #stories.storiesWrapper {
		 display: inline-block;
	 }
     .load-more-products {
         display: flex;
         justify-content: center;
        opacity: 0;
    }
    .load-more-products.show {
        opacity: 1;
    }
     .load-more-products .loader {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }
    .load-more-products .loader div {
        display: inline-block;
        position: absolute;
        left: 8px;
        width: 16px;
        background: #fcb800;
        animation: loader 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
    }
    .load-more-products .loader div:nth-child(1) {
        left: 8px;
        animation-delay: -0.24s;
    }
    .load-more-products .loader div:nth-child(2) {
        left: 32px;
        animation-delay: -0.12s;
    }
    .load-more-products .loader div:nth-child(3) {
        left: 56px;
        animation-delay: 0;
    }
    @keyframes loader {
        0% {
            top: 8px;
            height: 64px;
        }

        50%,
        100% {
            top: 24px;
            height: 32px;
        }
}
	 </style>
    @endpush
    @php
    @endphp
    <script>window['catswiper'] = [];</script>
    <section class="home-section">
        <div class="container">
            <div class="top-stories ">
                <div class="swiper-container mySwiperStories">
                    <div id="stories" class="swiper-wrapper storiesWrapper"></div>
                    <div class="swiper-scrollbar"></div>
                </div>
                <div class="icons left"><i class="fas fa-chevron-left"></i></div>
                <div class="icons right"><i class="fas fa-chevron-right"></i></div>
            </div>
        </div>
    </section>
    <section class="home-section">
        <div class="container">
            <div class="home-slider">
                <div class="categories-list">
                    <ul class="categories">
                        @foreach($categories as $pcategory)
                        <li class="categories__link @if(count($pcategory->children)) submenu @endif">
                            <a href="{{ route('category.single', $pcategory->slug) }}">
                                @if($pcategory->icon)
                                    <i class="{{ $pcategory->icon }} categories__icon"></i>
                                @else
                                    <img src="{{ ($pcategory->image)?asset('photos/taxonomy/'.$pcategory->image):'' }}" alt="" class="categories__icon">
                                @endif
                                {{ $pcategory->name }}
                                @if(count($pcategory->children))
                                    <div class="right-icon">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                @endif
                            </a>
                            @if($pcategory->children)
                                <div class="subcategories">
                                    <ul>
                                    @foreach($pcategory->children as $subcategory)
                                        <li class="subcategories__link">
                                            <a href="{{ route('category.single', $subcategory->slug) }}">{{$subcategory->name}}</a>
                                            @if($subcategory->children)
                                                <ul class="sub-cat">
                                                @foreach($subcategory->children as $subsCategory)
                                                    <li class="sub-cat__link">
                                                        <a href="{{ route('category.single', $subsCategory->slug) }}">{{$subsCategory->name}}</a>
                                                    </li>
                                                @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                    </ul>
                                </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @php
                    if (Cache::has('settings')) {
                        $allSettingss = Cache::get('asettings');
                    } else {
                        $allSettingss = Cache::rememberForever('asettings', function () {
                            return App\Models\Setting::all()->pluck('value', 'name');
                        });
                    }
                @endphp
                <div class="sliders">
                    <div class="sliders__left">
                        @if(isset($allSettingss['wslider1']))
                            @if(isset($allSettingss['wsliderLink1']))
                                <a href="{{ $allSettingss['wsliderLink1'] }}">
                            @endif
                            <img src="{{ asset('photos/images/'.$allSettingss['wslider1']) }}" alt="">
                            @if(isset($allSettingss['wsliderLink1']))
                                </a>
                            @endif
                        @endif
                    </div>
                    <div class="sliders__right">
                        @if(isset($allSettingss['wslider2']))
                            @if(isset($allSettingss['wsliderLink2']))
                                <a href="{{ $allSettingss['wsliderLink2'] }}">
                            @endif
                            <img src="{{ asset('photos/images/'.$allSettingss['wslider2']) }}" alt="">
                            @if(isset($allSettingss['wsliderLink2']))
                                </a>
                            @endif
                        @endif
                        @if(isset($allSettingss['wslider3']))
                            @if(isset($allSettingss['wsliderLink3']))
                                <a href="{{ $allSettingss['wsliderLink3'] }}">
                            @endif
                            <img src="{{ asset('photos/images/'.$allSettingss['wslider3']) }}" alt="">
                            @if(isset($allSettingss['wsliderLink3']))
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="home-section">
        <div class="container">
            <div class="shop-baner">
                @foreach($featuredProducts as $featured)
                    <div class="baner">
                        <div class="baner__image">
                            <img src="{{asset('/photos/images/'.$featured->image)}}" alt="">
                        </div>
                        <div class="baner__text">{!! $featured->name !!}</div>
                        @if($featured->link) <a href="{{ $featured->link }}" class="baner__link">{{ $featured->button }} @endif
                            <i class="fas fa-chevron-right"></i>
                        @if($featured->link) </a> @endif
                    </div>
                @endforeach
                @if($homeAds)
                    <div class="baner sads">
                        @if($homeAds->link)
                            <a href="{{ route('ads.link', $homeAds->id) }}">
                                <img src="{{ ($homeAds->dimage) ? route('single.ads', $homeAds->dimage) : ''}}" alt="">
                            </a>
                        @else
                            <img src="{{ ($homeAds->dimage) ? asset('photos/ads/'.$homeAds->dimage) : ''}}" alt="">
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
    <section class="home-section">
        <div class="container">
            <div class="features-block">
                @foreach($features as $feature)
                    <div class="feature-block">
                        <div class="feature-icon">
                            @if($feature->image)
                                <img src="{{ asset('photos/images/'.$feature->image) }}" alt="">
                            @else
                                <i class="{{ $feature->icon }}"></i>
                            @endif
                        </div>
                        <div class="feature-text">
                            <h3>{{ $feature->name }}</h3>
                            <span>{{ $feature->description }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="home-section">
        <div class="container">
            <div class="trending-tags boxshadow">
                <div class="section-title">Trendet më të fundit</div>
                @livewire('home.trending-categories')
            </div>
        </div>
    </section>
    @foreach($homeCategories as $category)
        <section class="home-section">
            <div class="container">
                <div class="cayegory-products boxshadow">
                    <livewire:home.categories-product :category="$category" :wire:key="$category->id">
                </div>
            </div>
        </section>
    @endforeach
    <section class="home-section">
        <div class="container">
            <livewire:home-products>
            {{-- <div class="allprodcucts">
                <div class="section-title">Të gjithë produktet</div>
                <div class="product-list flex">
                    @php
                        $isLoggedIn = false;
                        if(current_user()){
                            $isLoggedIn = current_user()->id;
                        }
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
                <div class="load-more-products">
                    <div class="loader">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
</x-app-layout>