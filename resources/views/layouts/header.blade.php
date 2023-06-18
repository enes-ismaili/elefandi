<header class="main-header">
    @php
        $now = Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $specialOffers = App\Models\Offer::where([['type', '=' ,4],['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]])->orderBy('discount', 'asc')->get();
    @endphp
    <div class="container">
        <div class="header-top">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ (isset($allSettings['logo'])) ? asset('photos/images/'.$allSettings['logo']) : asset('images/logo.png')}}" alt="">
                </a>
            </div>
            <div class="search-menu">
                <div class="search-icon"><i class="fas fa-search"></i></div>
            </div>
            <livewire:header.search :categories="$allCategories">
            {{-- <form action="{{ route('search.post') }}" method="POST" class="main-search form-control">
                @csrf
                <div class="select-box">
                    <div class="select-box__current" tabindex="1">
                        <div class="select-box__value">
                            <input class="select-box__input" type="radio" id="0" value="0" name="search_categories" checked="checked" />
                            <p class="select-box__input-text">Të gjitha</p>
                        </div>
                        @foreach($allCategories as $category)
                            <div class="select-box__value">
                                <input class="select-box__input" type="radio" id="{{$category->id}}" value="{{$category->id}}" name="search_categories" />
                                <p class="select-box__input-text">{{$category->name}}</p>
                            </div>
                        @endforeach
                        <img class="select-box__icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true" />
                    </div>
                    <ul class="select-box__list">
                        <li><label class="select-box__option" for="0" aria-hidden="aria-hidden">Të gjitha</label></li>
                        @foreach($allCategories as $category)
                            <li><label class="select-box__option" for="{{$category->id}}" aria-hidden="aria-hidden">{{$category->name}}</label></li>
                        @endforeach
                    </ul>
                </div>
                <input type="text" class="form-control" id="search" name="squery" required>
                <button type="submit"><i class="fas fa-search"></i>Kërko</button>
            </form> --}}
            {{-- <div class="mobile-menu"><i class="fas fa-bars"></i></div> --}}
            <div class="right-header">
                <div class="menu-title dmobile">Menu <i class="close-popup v2"></i></div>
                <div class="chat-header">
                    @livewire('header.mini-chat')
                </div>
                <div class="wishlist">
                    @livewire('header.wishlist')
                </div>
                <div class="mini-cart">
                    @livewire('header.mini-cart')
                </div>
                <div class="country">
                    <div class="current-country">
                        <img src="{{ asset('images/1.png')}}" alt="">
                        <span class="dmobile icon-title">Shteti</span>
                    </div>
                    <div class="select-countries">
                        <ul>
                            <li data-id="1">Kosovë</li>
                            <li data-id="2">Shqipëri</li>
                            <li data-id="3">Maqedoni</li>
                        </ul>
                    </div>
                    <script>
                        window.getCookie = function(name){
                            let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                            if (match) return match[2];
                        }
                        window.setCookie = function(name,value,year) {
                            let date = new Date();
                            let expireD = (3*3110400*10000);
                            if(year){
                                expireD = year * 60 * 60 * 24 * 1000;
                            }
                            date.setTime(date.getTime() + expireD);
                            let expires = "; expires=" + date.toUTCString();
                            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
                        }
                        let currentLocation = 1;
                        if(window.getCookie('country_id')){
                            currentLocation = window.getCookie('country_id');
                        } else {
                            window.setCookie('country_id', 1)
                        }
                        let selectCountry = document.querySelector('.current-country');
                        if(selectCountry){
                            selectCountry.querySelector('img').src = '/images/'+currentLocation+'.png';
                        }
                        let selectCountries = document.querySelectorAll('.select-countries li');
                        selectCountries.forEach(selectCountryL => {
                            selectCountryL.addEventListener('click', e=>{
                                currentLocation = e.target.dataset.id;
                                window.setCookie('country_id', currentLocation)
                                if(selectCountry){
                                    selectCountry.querySelector('img').src = '/images/'+currentLocation+'.png';
                                }
                                let mainBody1 = document.getElementsByTagName('body')[0];
                                if(mainBody1.classList.contains('opencat')){
                                    mainBody1.classList.remove('opencat');
                                }
                                if(mainBody1.classList.contains('opencountry')){
                                    mainBody1.classList.remove('opencountry');
                                }
                                if(!mainBody1.classList.contains('openmenu')){	
                                    mainBody1.classList.remove('openbg');	
                                }
                                window.livewire.emitTo('products.product-price', 'updatedCart');
                                window.livewire.emitTo('header.login-user', 'changeCountry', '@if(Request::is('checkout'))1 @endif');
                                window.livewire.emitTo('cart.view-cart', 'change-countries');
                                window.livewire.emitTo('products.product-shipping-offer', 'updateCountry', currentLocation);
                                @if(Request::routeIs('view.checkout'))
                                    window.location.replace('{{ route('view.cart') }}');
                                @endif
                            })
                        })
                        let viewedProductsH = localStorage.getItem('viewed');
                        if(viewedProductsH){
                            document.addEventListener("DOMContentLoaded", () => {
                                // viewedProductsH = JSON.parse(viewedProductsH);
                                // window.livewire.emitTo('products.product-price', 'updatedCart');
                                window.livewire.emitTo('header.watched-products', 'getWatched', viewedProductsH);
                            })
                        }
                        window.addEventListener('reInitWatchedSwiper', event => {
                            setTimeout(() => {
                                window.initSwiper('watchedSwiper', {
                                    slidesPerView: 8,
                                    spaceBetween: 10,
                                    direction: 'horizontal',
                                    loop: false,
                                    initialSlide: 0,
                                });
                            }, 200);
                        });
                    </script>
                </div>
                @if (Route::has('login'))
                    @auth
                        <div class="user-profile m">
                            <div class="profile-icon icon">
                                <i class="icon-user1"></i>
                            </div>
                            <div class="profile-info">
                                <div class="profile-name">{{current_user()->fullName()}} <i class="close-popup v2"></i></div>
                                <div class="profile-more">
                                    <div class="more"><img class="select-box__icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true"></div>
                                </div>
                                <div class="profile-menu">
                                    <ul class="">
                                        <li><a href="{{route('profile.edit')}}">Profili</a></li>
                                        <li><a href="{{ route('profile.orders.index') }}">Porositë</a></li>
                                        <li><a href="{{ route('profile.address') }}">Adresat</a></li>
                                        @if(current_user()->vendor())
                                            <li><a href="{{ route('vendor.home') }}">Dyqani im</a></li>
                                        @endif
                                        @if(current_user() && count(current_user()->aroles))
                                            <li><a href="{{ route('admin.home') }}">Administrator</a></li>
                                        @endif
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a class="" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Dil</a>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="user-profile">
                            <div class="profile-icon icon">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="profile-info">
                                @livewire('header.login-user')
                            </div>
                        </div>
                    @endauth
                @endif
                <div class="dmobile more-menu">	
                    <ul>	
                        @if(current_user() && current_vendor())	
                        @else	
                            <li><a href="{{ route('home.vendor') }}" class="register-vendor">Krijo Dyqanin tuaj</a></li>	
                        @endif	
                        <li><a href="{{ route('view.track') }}" class="track-order">Gjurmo Porosinë tuaj</a></li>	
                    </ul>
                    @if($specialOffers->count())
                    <div class="special-offers">
                        <h5>Ofertat</h5>
                        <ul>
                            @foreach($specialOffers as $soffer)
                                <li>
                                    <a href="{{ route('offer.single', $soffer->id) }}">{{ strtoupper($soffer->name) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="seperator"></div>
    <div class="container">
        <div class="header-bottom">
            <div class="categories">
                <div class="categories__button">
                    <div class="categories__icon">
                        <i class="fas fa-bars"></i>
                    </div>
                    <div class="categories__text">Kategoritë</div>
                </div>
                @if($specialOffers->count())
                <div class="other-links">
                    @foreach($specialOffers as $soffer)
                        <a href="{{ route('offer.single', $soffer->id) }}">{{ strtoupper($soffer->name) }}</a>
                    @endforeach
                </div>
                @endif
                <div class="categories-list">
                    <div class="categories-open"></div>
                    <div class="categories-title dmobile">Te gjitha Kategorite <i class="close-popup v2"></i></div>
                    <ul class="categories">
                        @foreach($allCategories as $pcategory)
                        <li class="categories__link @if(count($pcategory->children))submenu @endif">
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
            </div>
            <div class="viewed_products">
                {{-- <div class="viewed_products_button">Produktet e shikuara së fundmi<i class="fas fa-chevron-down"></i></div> --}}
                <livewire:header.watched-products >
            </div>
            <div class="right-header">
                @if(current_user() && current_vendor())
                @else
                    <a href="{{ route('home.vendor') }}" class="register-vendor">Krijo Dyqanin tuaj</a>
                @endif
                <a href="{{ route('view.track') }}" class="track-order">Gjurmo Porosinë tuaj</a>
            </div>
        </div>
    </div>
</header>
@if(Session::has('ordersuccess'))
<style>
    .order-success {
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
<div class="container">
    <div class="alert fade alert-success alert-dismissible show">
        <button type="button" class="close font__size-18" data-dismiss="alert">
            <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
        </button>
        <i class="start-icon far fa-check-circle faa-tada animated"></i>
        <b>{{ Session::get('ordersuccess') }}</b>
    </div>
</div>
@endif
@if(Session::has('synclocal'))
    @livewire('header.sync-local-storage')
@endif
@if(Session::has('clearlocal'))
<script>
    let newCartString = JSON.stringify({});
    window.localStorage.setItem('cart', newCartString);
</script>
@endif