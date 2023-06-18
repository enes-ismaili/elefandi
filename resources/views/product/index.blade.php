<x-app-layout>
    @push('scripts')
        <script type="module">
            import Swiper from '{{ asset('js/swiper.min.js') }}';
            const swiper = new Swiper(".mySwiper", {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
                breakpoints: {
                    100: {
                        slidesPerView: 4,
                        direction: "horizontal",
                    },
                    500: {
                        slidesPerView: 5,
                        direction: "vertical",
                    },
                    766: {
                        slidesPerView: 4,
                        direction: "horizontal",
                    }
                },
            });
            const swiper2 = new Swiper(".mySwiper2", {
                spaceBetween: 10,
                autoHeight: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                thumbs: {
                    swiper: swiper,
                },
            });
            const similarSwiper = new Swiper(".similarSwiper", {
              slidesPerView: 5,
              spaceBetween: 10,
              pagination: {
                el: ".swiper-pagination",
                clickable: true,
              },
              lazy: true,
              breakpoints: {
                100: {
                  slidesPerView: 1,
                  spaceBetween: 20,
                },
                300: {
                  slidesPerView: 2,
                  spaceBetween: 10,
                },
                450: {
                  slidesPerView: 2,
                  spaceBetween: 20,
                },
                570: {
                  slidesPerView: 3,
                  spaceBetween: 10,
                },
                766: {
                  slidesPerView: 4,
                  spaceBetween: 40,
                },
                1024: {
                  slidesPerView: 5,
                  spaceBetween: 50,
                },
              },
            });
            
            if(window.getCookie('disableQr') != 'true'){
                document.querySelector('.sticky-qrcode').classList.add('show');
            }
            let closeQr = document.querySelector('.close-qr');
            if(closeQr){
                closeQr.addEventListener('click', (e)=>{
                    window.setCookie('disableQr', 'true', 10);
                    e.target.parentElement.parentElement.classList.remove('show');
                })
            }
        </script>
        <script>
            let viewedProducts = localStorage.getItem('viewed');
            if(viewedProducts){
                viewedProducts = JSON.parse(viewedProducts);
                let checkExis = viewedProducts.indexOf({{ $product->id }});
                if(checkExis >= 0){
                    viewedProducts.splice(checkExis, 1);
                }
            } else {
                viewedProducts = [];
            }
            viewedProducts.push({{ $product->id }});
            if(viewedProducts.length > 15){
                let removeItems = viewedProducts.length - 15;
                viewedProducts.splice(0, removeItems);
            }
            localStorage.setItem('viewed', JSON.stringify(viewedProducts));
        </script>
    @endpush
    @push('styles')
    <link  rel="stylesheet" href="{{ asset('css/single.css') }}">
    @endpush
    @section('pageTitle', $product->name)
    <script src="{{ asset('js/countDown.js') }}"></script>
    <script>
        let cd;
        let newOffDate = '';
        window.addEventListener('initCountDown', event => {
            if(event.detail == 'stop'){
                if(cd){
                    cd.stop();
                    cd.interval = null;
                    cd.digitConts = {};
                    cd ='';
                }
            } else {
                newOffDate = +event.detail;
                if(cd){
                    cd.stop();
                    cd.interval = null;
                    cd.digitConts = {};
                    cd.options.date = newOffDate;
                    cd.start();
                } else {
                    cd = new Countdown({
                        cont: document.querySelector('.container-countdown1'),
                        date: newOffDate,
                        outputTranslation: {
                            year: 'Vite',
                            week: 'Javë',
                            day: 'Ditë',
                            hour: 'Orë',
                            minute: 'Minuta',
                            second: 'Sekonda',
                        },
                        endCallback: null,
                        outputFormat: 'day|hour|minute|second',
                    });
                    cd.start();
                }
            }
        });
    </script>
    @if(!current_user())
        @php
            $cuuid = (isset($_COOKIE['uuids']) && $_COOKIE['uuids']) ? $_COOKIE['uuids'] : '';
        @endphp
        <script>
            console.log('{{$cuuid}}');
            if(!'{{$cuuid}}'){
                setTimeout(function(){
                    window.location.reload()
                },500);
            }
        </script>
    @endif
    @php
        if($product->ratings->count()){
            $ratingAverage = $product->ratings->pluck('rating')->avg();
            $ratingAverageRoundS = round(($ratingAverage*10)/5) * 5;
            if(Str::length($ratingAverage) == 1){
                $ratingAverageF = $ratingAverage.'.0';
            } else {
                $ratingAverageRound = round($ratingAverage, 2);
                $ratingAverageF = $ratingAverageRound;
            }
        } else {
            $ratingAverageF = '0.0';
            $ratingAverageRoundS = 0;
        }
    @endphp
    <div class="container">
        <div class="single-product">
            <div class="product-main">
                <div class="left">
                    <livewire:products.product-shipping-offer :vendor="$product->owner">
                    <div class="product-main-info">
                        <div class="product-gallery">
                            <div class="swiper-container mySwiper2">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <img src="{{ asset('/photos/products/'.$product->image) }}" />
                                    </div>
                                    @if($product->gallery->count())
                                    @foreach($product->gallery as $gallery)
                                        <div class="swiper-slide">
                                            <img src="{{ asset('/photos/products/'.$gallery->image) }}" />
                                        </div>
                                    @endforeach
                                    @endif
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                            <div thumbsSlider="" class="swiper-container mySwiper">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        @if(file_exists(public_path('/photos/products/70/'.$product->image)))
                                            <img src="{{ asset('/photos/products/70/'.$product->image) }}" />
                                        @else
                                            <img src="{{ asset('/photos/products/'.$product->image) }}" />
                                        @endif
                                    </div>
                                    @if($product->gallery->count())
                                    @foreach($product->gallery as $gallery)
                                        <div class="swiper-slide">
                                            @if( file_exists(public_path('/photos/products/70/'.$gallery->image)) )
                                                <img src="{{ asset('/photos/products/70/'.$gallery->image) }}" />
                                            @else
                                                <img src="{{ asset('/photos/products/'.$gallery->image) }}" />
                                            @endif
                                        </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="product-information">
                            @error('product_rating') <span class="text-danger error errating">* Ju duhet te zgjidhni së paku një yll për të vlersuar produktin</span>@enderror
                            <div class="title">{{$product->name}}</div>
                            <div class="row">
                                <div class="col-7">
                                    @if($product->owner)
                                        <div class="vendor">Dyqani: <br><a href="{{ route('single.vendor', $product->owner->slug) }}">{!! $product->owner->name !!}</a>
                                            @if($product->owner->verified)
                                            <img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="col-5">
                                    <div class="product-rating">
                                        <div class="ratings r{{ $ratingAverageRoundS }}">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                        </div>
                                        <div>
                                            <span class="total-votes">({{ ($product->ratings->count() == 1) ? $product->ratings->count() .' votë' : $product->ratings->count() . ' vota' }})</span>
                                            @if($product->sales || ($product->psales && $product->psales->fsales))
                                                <span class="total-orders">
                                                    (@if($product->psales && $product->psales->fsales){{ ($product->psales->saction == 2) ? ($product->sales * $product->psales->fsales) : ($product->sales + $product->psales->fsales) }}
                                                    @else{{ $product->sales }}@endif
                                                    shitje)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($hasBuy)
                                <div class="has-buy-rating">
                                    <span class="goToRating">Ju keni blerë këtë produkt. Lini komentin dhe vlersimin tuaj</span>
                                </div>
                            @endif
                            <div class="divider"></div>
                            @livewire('products.product-price', ['product'=> $product])
                        </div>
                    </div>
                    <div class="product-tabs tab-list">
                        <ul class="tab-header">
                            <li class="active"><span data-id="tab-1">Përshkrimi</span></li>
                            <li><span data-id="tab-2">Specifikimet</span></li>
                            <li><span data-id="tab-3">Dyqani</span></li>
                            <li><span data-id="tab-4">Vlerësimet</span></li>
                            <li><span data-id="tab-5">Politika e kthimit</span></li>
                        </ul>
                        <div class="tabs">
                            <div class="tab active" id="tab-1">
                                <div class="content">
                                    {!! $product->description !!}
                                </div>
                            </div>
                            <div class="tab" id="tab-2">
                                <table class="table table-bordered product-specification">
                                    <tbody>
                                        <tr>
                                            <td>Pesha</td>
                                            <td>{{ $product->weight }}</td>
                                        </tr>
                                        <tr>
                                            <td>Madhësia</td>
                                            <td>{{ $product->size }}</td>
                                        </tr>
										@foreach($product->specification as $specification)
											<tr>
												<td>{{ $specification->name }}</td>
												<td>{{ $specification->value }}</td>
											</tr>
										@endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab" id="tab-3">
                                <div class="product-vendor">
                                    @if($product->owner->logo_path)<div class="avatar"><img src="{{ asset('/photos/vendor/'.$product->owner->logo_path) }}"></div>@endif
                                    <div class="vendor-info">
                                        <h3 class="name"><a href="{{ route('single.vendor', $product->owner->slug) }}">{{$product->owner->name}}</a> <img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}"></h3>
                                        <div class="description">{{$product->owner->description}}</div>
                                    </div>
                                </div>
                                <div class="product-vendor-shipping">
                                    <h4>Transporti rreth dyqanit tonë</h4>
                                    @foreach($shippingCountry as $country)
                                        @php
                                            $vendorShipping = $product->owner->shippings()->where('country_id', '=', $country->id)->first();
                                            $vendorName = $product->owner->name;
                                            if($vendorShipping){
                                                if($vendorShipping->limit){
                                                    $shippingLimit = $vendorShipping->limit;
                                                    $shippingCost = $vendorShipping->cost;
                                                } else {
                                                    $shippingLimit = '';
                                                    $shippingCost = false;
                                                }
                                                $shippingTrans = $vendorShipping->transport;
                                                if($shippingTrans == 2  && !$shippingLimit){
                                                    $shippingTrans = 1;
                                                }
                                                if($shippingTrans == 4 && !$shippingLimit){
                                                    $shippingTrans = 3;
                                                }
                                            } else {
                                                $shippingLimit = '';
                                                $shippingCost = 'Falas';
                                                $shippingTrans = 1;
                                            }
                                        @endphp
                                        @if($shippingTrans == 1)
                                            <p><span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron tranport normal për secilin produkt</p>
                                        @elseif($shippingTrans == 2)
                                            <p><span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron transport falas mbi {{$shippingLimit}}€</p>
                                        @elseif($shippingTrans == 3)
                                            <p><span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron shërbimin blej dy ose më shumë produkte dhe paguaj tranportin vetëm për njërin produkt</p>
                                        @elseif($shippingTrans == 4)
                                            <p><span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron transport falas mbi {{$shippingLimit}}€<br>
                                            <span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron shërbimin blej dy ose më shumë produkte dhe paguaj tranportin vetëm për njërin produkt</p>
                                        @endif
                                        {{-- @if($shippingCost)
                                            <p><span class="country">{{$country->name}}</span> - "{{$vendorName}}" ofron transport falas mbi {{$shippingCost}}€</p>
                                        @endif --}}
                                    @endforeach
                                </div>
                            </div>
                            <div class="tab" id="tab-4">
                                @if($hasBuy)
                                    <div class="product-rating-add">
                                        <form action="{{ route('product.comment', $product->id) }}" method="post">
                                            @csrf
                                            <h3>Jepni vlersimin tuaj për këtë produkt</h3>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group add-rating">
                                                        <label for="rating">Vlersimi Juaj *</label>
                                                        <div class="rating-star">
                                                            <input type="radio" name="product_rating" value="1" />
                                                            <input type="radio" name="product_rating" value="2" />
                                                            <input type="radio" name="product_rating" value="3" />
                                                            <input type="radio" name="product_rating" value="4" />
                                                            <input type="radio" name="product_rating" value="5" />
                                                            <i></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="comment">Komenti Juaj</label>
                                                        <textarea name="comment" id="comment" class="form-control" placeholder="Komenti juaj">{{ old('comment') }}</textarea>
                                                        @error('comment') <span class="text-danger error">{{ $message }}</span>@enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn c3 small mb-15">Shto</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="divider"></div>
                                @endif
                                <div class="product-rating">
                                    <div class="rating-show">
                                        <div class="ratings-num">{{ $ratingAverageF }}</div>
                                        <div class="ratings r{{ $ratingAverageRoundS }}">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-comments">
                                        <div class="title">Vlerësimet</div>
                                        @if($product->ratings->count())
                                            @foreach($product->ratings as $rating)
                                                <div class="single-comment">
                                                    <div class="user">
                                                        {{ ($rating->user) ? $rating->user->first_name.' '.$rating->user->last_name : '' }}
                                                        <div class="ratings r{{ (Str::length($rating->rating) == 1) ? $rating->rating.'0' : $rating->rating }}">
                                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="comment">{{ $rating->comment }}</div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="no-comments">Nuk ka vlerësime për këtë produkt.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="tab" id="tab-5">
                                @if($product->owner->pages)
                                    <div class="content tmce">
                                        {!! $product->owner->pages->kthimi !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="card mt-20">
                        <div class="card-body">
                            @if($singleAds)
                            <div class="sads">
                                @if($singleAds->link)
                                    <a href="{{ route('ads.link', $singleAds->id) }}">
                                        <img src="{{ ($singleAds->dimage) ? route('single.ads', $singleAds->dimage) : ''}}" alt="">
                                    </a>
                                @else
                                    <img src="{{ ($singleAds->dimage) ? asset('photos/ads/'.$singleAds->dimage) : ''}}" alt="">
                                @endif
                            </div>
                            <div class="divider"></div>
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <div class="product-qr">
                                        <div class="title">Shiko Produktin në Telefon</div>
                                        <div class="stitle">Skano kodin dhe blej produktin</div>
                                        <div class="qr-code"><img src="{{ asset('photos/qrcodes/products/'.$product->qrcode) }}"></div>
                                        <div class="btitle">Shpejt dhe lehtë!</div>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="product-options">
                                {{-- <div class="report-article">Raportoni Artikullin</div> --}}
                                <livewire:products.report-product :productId="$product->id">
                                @if($product->sku)
                                    <div class="sku">SKU: <span>{{$product->sku}}</span></div>
                                @endif
                                @if($product->category)
                                    <div class="category">Kategoria: <a href="{{ route('category.single', $product->category->slug) }}">{{$product->category->name}}</a></div>
                                @endif
                                @if(count($product->brands)>0)
                                    <div class="brands">Marka: <span>
                                        @foreach($product->brands as $brand)
                                            <a href="{{ route('brand.single', $brand->slug) }}">{{$brand->name}}</a>
                                        @endforeach
                                    </span></div>
                                @endif
                                @if(count($product->tags)>0)
                                    <div class="tags">Etiketimet: <span>
                                        @foreach($product->tags as $tag)
                                            <a href="{{ route('tag.single', $tag->slug) }}">#{{$tag->name}}</a>
                                        @endforeach
                                    </span></div>
                                @endif
                            </div>
                            <div class="divider"></div>
                            <div class="column nv">
                                <div class="col-row jc">
                                    <div class="left">
                                        <div class="icons round border">
                                            <svg id="shop" xmlns="http://www.w3.org/2000/svg" width="21.381" height="18.262" viewBox="0 0 21.381 18.262">
                                                <g id="Group_463" data-name="Group 463" transform="translate(0 0)">
                                                <g id="Group_462" data-name="Group 462">
                                                    <path id="Path_1365" data-name="Path 1365" d="M21.35,42.86l-.014-.08c0-.012,0-.025-.007-.037l-1.5-3.621V37.34H1.527v1.782L.052,42.743c0,.014-.005.029-.008.043l-.014.075a2.578,2.578,0,0,0-.031.4A2.544,2.544,0,0,0,1.336,45.5V55.6H20.045V45.5a2.545,2.545,0,0,0,1.336-2.24A2.578,2.578,0,0,0,21.35,42.86Zm-1.007-.175H17.07l-.393-2.645h2.589ZM2.418,38.231H18.939v.891H2.418ZM16.3,43.576l.048.323-.264.351a1.654,1.654,0,0,1-2.648,0l-.269-.358-.016-.316Zm-3.191-.891-.13-2.645h2.795l.394,2.645Zm-.848.891h0l.016.323-.264.351a1.654,1.654,0,0,1-2.648,0l-.27-.361.016-.314Zm-3.106-.891L9.29,40.04h2.8l.129,2.645Zm-3.94,0L5.6,40.04H8.4l-.134,2.645Zm3,.891L8.2,43.9l-.261.347a1.654,1.654,0,0,1-2.648,0l-.257-.342.049-.332Zm-6.1-3.536H4.7l-.389,2.645H1.038ZM.921,43.576H4.185l-.07.346-.246.328a1.655,1.655,0,0,1-2.948-.674ZM17.372,54.712H13.808V51.593h.446a.445.445,0,1,0,0-.89h-.446V47.585h3.564Zm1.781,0h-.891V46.695H12.917v8.018H2.227V45.783a2.545,2.545,0,0,0,2.354-1,2.546,2.546,0,0,0,4.073,0,2.545,2.545,0,0,0,4.072,0,2.546,2.546,0,0,0,4.073,0,2.545,2.545,0,0,0,2.354,1v8.929Zm-.319-9.8a1.642,1.642,0,0,1-1.323-.662l-.259-.344-.05-.33h3.256A1.656,1.656,0,0,1,18.835,44.912Z" transform="translate(0 -37.34)" fill="#434343"/>
                                                </g>
                                                </g>
                                                <g id="Group_465" data-name="Group 465" transform="translate(3.118 9.355)">
                                                <g id="Group_464" data-name="Group 464">
                                                    <path id="Path_1366" data-name="Path 1366" d="M74.656,261.348v6.235h8.909v-6.235Zm8.018,5.345H75.547v-4.455h7.127Z" transform="translate(-74.656 -261.348)" fill="#434343"/>
                                                </g>
                                                </g>
                                                <g id="Group_467" data-name="Group 467" transform="translate(8.905 12.472)">
                                                <g id="Group_466" data-name="Group 466">
                                                    <path id="Path_1367" data-name="Path 1367" d="M214.586,336a.442.442,0,0,0-.315.13l-.891.891a.445.445,0,0,0,.63.629l.891-.89a.446.446,0,0,0-.314-.761Z" transform="translate(-213.25 -336.004)" fill="#434343"/>
                                                </g>
                                                </g>
                                                <g id="Group_469" data-name="Group 469" transform="translate(7.123 10.691)">
                                                <g id="Group_468" data-name="Group 468">
                                                    <path id="Path_1368" data-name="Path 1368" d="M173.7,293.348a.442.442,0,0,0-.315.13l-2.673,2.672a.445.445,0,1,0,.63.63l2.673-2.672a.445.445,0,0,0-.314-.76Z" transform="translate(-170.578 -293.348)" fill="#434343"/>
                                                </g>
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="right">Keni dicka për të shitur? <br><a href="#" class="link c1">Krijo dyqanin tuaj!</a></div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="column v1">
                                <div class="col-row">
                                    <div class="left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="31.292" height="31.289" viewBox="0 0 31.292 31.289">
                                            <g id="globe" transform="translate(0 -0.022)">
                                              <g id="Group_444" data-name="Group 444" transform="translate(0 0.022)">
                                                <path id="Path_1321" data-name="Path 1321" d="M15.627.022a15.669,15.669,0,0,0-1.679.09,14.769,14.769,0,0,0-1.514.242q-.145.03-.289.064A15.647,15.647,0,0,0,4.427,4.739q-.293.3-.574.619A15.664,15.664,0,0,0,26.83,26.632q.294-.3.574-.619A15.663,15.663,0,0,0,15.627.022Zm-10.5,5.5c.082-.086.168-.168.252-.251s.194-.192.293-.284.177-.16.266-.239.2-.179.3-.265.184-.15.277-.225.209-.167.316-.247.192-.141.289-.209.217-.157.327-.229.2-.132.3-.2.224-.142.337-.209.2-.121.308-.18.232-.13.348-.193.209-.11.313-.163.239-.118.36-.175c.1-.05.213-.1.321-.146l.371-.157c.108-.044.216-.088.325-.129.126-.048.254-.093.382-.138.109-.038.218-.076.329-.112.13-.042.261-.08.393-.119.11-.032.219-.065.33-.095l.127-.031A12.955,12.955,0,0,0,8.653,6.952,17.537,17.537,0,0,1,5.076,5.575C5.093,5.557,5.108,5.538,5.123,5.523Zm-.751.841A18.141,18.141,0,0,0,8.334,7.94,27.094,27.094,0,0,0,7.28,15.164H1.02A14.588,14.588,0,0,1,4.372,6.363Zm0,18.645a14.59,14.59,0,0,1-3.352-8.8H7.28a27.091,27.091,0,0,0,1.054,7.224A18.153,18.153,0,0,0,4.372,25.008Zm7.488,4.8c-.105-.027-.215-.061-.323-.093-.132-.039-.265-.077-.4-.12-.11-.036-.219-.074-.327-.112-.128-.044-.257-.09-.384-.138-.109-.041-.216-.085-.323-.128l-.373-.157q-.16-.07-.318-.145c-.122-.057-.243-.116-.365-.176-.1-.052-.209-.1-.313-.161s-.235-.128-.352-.195-.2-.117-.3-.178-.228-.14-.34-.213-.2-.127-.3-.193-.221-.154-.331-.232-.191-.137-.285-.209-.214-.166-.32-.25-.184-.147-.275-.222-.205-.177-.306-.267-.178-.157-.265-.237-.2-.19-.293-.285-.17-.165-.252-.25L5.073,25.8a17.524,17.524,0,0,1,3.578-1.377,12.959,12.959,0,0,0,3.337,5.419C11.945,29.83,11.9,29.821,11.86,29.81Zm3.245.454c-2.234-.317-4.193-2.649-5.425-6.093a26.534,26.534,0,0,1,5.425-.639v6.731Zm0-7.776a27.5,27.5,0,0,0-5.753.686,26.12,26.12,0,0,1-1.028-6.967h6.781Zm0-7.325H8.324A26.115,26.115,0,0,1,9.353,8.2a27.505,27.505,0,0,0,5.753.686Zm0-7.325A26.506,26.506,0,0,1,9.68,7.2c1.232-3.443,3.191-5.775,5.425-6.093V7.839ZM26.883,6.363a14.59,14.59,0,0,1,3.352,8.8h-6.26A27.092,27.092,0,0,0,22.921,7.94,18.16,18.16,0,0,0,26.883,6.363Zm-7.493-4.8c.11.027.219.062.327.093.132.039.265.077.4.12.11.036.219.074.327.112.128.044.257.09.384.138.109.041.216.085.323.128l.373.157q.16.07.318.145c.122.057.243.116.365.176.1.052.209.1.313.161s.235.128.352.195.2.117.3.178.228.14.34.213.2.127.3.193.221.153.33.231.192.137.286.209.213.165.319.25.184.146.275.222.205.177.306.267.178.157.265.237.2.19.293.285.17.165.252.25l.045.049A17.524,17.524,0,0,1,22.6,6.948,12.958,12.958,0,0,0,19.263,1.53C19.305,1.541,19.348,1.55,19.39,1.561ZM16.15,1.107c2.234.317,4.193,2.649,5.425,6.093a26.534,26.534,0,0,1-5.425.639Zm0,7.776A27.5,27.5,0,0,0,21.9,8.2a26.12,26.12,0,0,1,1.028,6.967H16.15Zm0,7.325H22.93A26.115,26.115,0,0,1,21.9,23.175h0a27.5,27.5,0,0,0-5.753-.686Zm0,14.056V23.533a26.506,26.506,0,0,1,5.425.639C20.342,27.615,18.384,29.947,16.15,30.264Zm9.983-4.415c-.082.085-.168.167-.252.25s-.194.192-.294.285-.176.157-.265.238-.2.18-.305.266-.184.15-.277.224-.209.167-.316.247-.192.141-.289.209-.217.157-.327.229-.2.132-.3.2-.224.142-.338.209-.2.121-.307.18-.232.13-.349.193-.209.11-.313.163-.239.118-.36.175c-.1.05-.213.1-.321.146l-.371.157c-.108.044-.216.088-.324.129-.127.049-.255.094-.384.138-.109.038-.217.076-.327.112-.131.042-.261.081-.394.119-.109.032-.219.065-.329.095l-.127.031A12.955,12.955,0,0,0,22.6,24.419,17.537,17.537,0,0,1,26.179,25.8C26.162,25.815,26.147,25.833,26.132,25.849Zm.751-.841a18.141,18.141,0,0,0-3.962-1.577,27.094,27.094,0,0,0,1.054-7.224h6.26A14.588,14.588,0,0,1,26.883,25.008Z" transform="translate(0 -0.022)"/>
                                              </g>
                                            </g>
                                          </svg>
                                    </div>
                                    <div class="right">Dërgesa falas në të gjithë vendin.</div>
                                </div>
                                <div class="col-row">
                                    <div class="left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32.391" height="33.238" viewBox="0 0 32.391 33.238">
                                            <g id="Group_443" data-name="Group 443" transform="translate(-1041.673 -608.057)">
                                              <path id="Path_1319" data-name="Path 1319" d="M1076.6,657.457c0-1.243-.056-2.489.031-3.725a1.7,1.7,0,0,1,.661-1.206q3.814-2.188,7.719-4.215a1.743,1.743,0,0,1,1.394.034c2.543,1.327,5.041,2.737,7.568,4.094a1.279,1.279,0,0,1,.792,1.271q-.043,3.732,0,7.464a1.359,1.359,0,0,1-.817,1.361c-2.529,1.354-5.028,2.764-7.571,4.088a1.732,1.732,0,0,1-1.387,0c-2.544-1.323-5.041-2.739-7.573-4.085a1.387,1.387,0,0,1-.822-1.449c.044-1.211.012-2.424.012-3.636Zm2.39-4.156c2.112,1.18,4.066,2.3,6.058,3.351a1.557,1.557,0,0,0,1.214-.033c1.318-.626,2.6-1.33,3.883-2.028.709-.385,1.394-.815,2.183-1.28-2.161-1.178-4.192-2.292-6.235-3.383a.887.887,0,0,0-.7-.076C1083.286,650.961,1081.2,652.1,1078.985,653.3Zm14.216,1.3c-2.224,1.225-4.332,2.378-6.428,3.555a.728.728,0,0,0-.325.523c-.023,2-.015,4-.015,6.176,2.25-1.23,4.34-2.362,6.412-3.524a.823.823,0,0,0,.34-.605C1093.212,658.731,1093.2,656.737,1093.2,654.6Zm-15.061.023c0,2.119-.01,4.093.014,6.065a.829.829,0,0,0,.307.621c2.076,1.167,4.17,2.3,6.408,3.526,0-2.2.015-4.224-.024-6.252,0-.2-.313-.448-.537-.573C1082.309,656.9,1080.3,655.8,1078.14,654.623Z" transform="translate(-28.216 -32.435)"/>
                                              <path id="Path_1320" data-name="Path 1320" d="M1044.66,616.82c.944-.153,1.756-.292,2.569-.415.545-.082,1.151-.044,1.119.64-.015.31-.52.775-.875.857-1.333.309-2.7.487-4.047.718a.792.792,0,0,1-1.045-.695c-.256-1.444-.541-2.886-.706-4.341-.035-.306.4-.665.611-1,.3.271.733.49.857.825a12.4,12.4,0,0,1,.4,2.034c.661-.786,1.2-1.494,1.809-2.142a16.609,16.609,0,1,1,12.983,27.969c-.064,0-.127.007-.191.008-.6.01-1.356.149-1.419-.708-.065-.889.687-.812,1.3-.836a15.027,15.027,0,0,0,13.679-19.831,14.245,14.245,0,0,0-12.1-10.119c-6-.87-10.871,1.4-14.5,6.271A8.609,8.609,0,0,0,1044.66,616.82Z" transform="translate(0 0)"/>
                                            </g>
                                          </svg>
                                    </div>
                                    <div class="right">Kthe produktet brenda 7 ditëve nëse diqka nuk është në rregull.</div>
                                </div>
                                <div class="col-row">
                                    <div class="left">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24.185" height="32.247" viewBox="0 0 24.185 32.247">
                                            <g id="invoice" transform="translate(-64)">
                                              <g id="Group_446" data-name="Group 446" transform="translate(64)">
                                                <g id="Group_445" data-name="Group 445" transform="translate(0)">
                                                  <path id="Path_1322" data-name="Path 1322" d="M87.989,6.915,81.27.2A.671.671,0,0,0,80.8,0H66.687A2.69,2.69,0,0,0,64,2.687V29.56a2.69,2.69,0,0,0,2.687,2.687H85.5a2.69,2.69,0,0,0,2.687-2.687V7.39A.671.671,0,0,0,87.989,6.915ZM81.467,2.294l4.425,4.425H82.811a1.345,1.345,0,0,1-1.344-1.344ZM86.842,29.56A1.345,1.345,0,0,1,85.5,30.9H66.687a1.345,1.345,0,0,1-1.344-1.344V2.687a1.345,1.345,0,0,1,1.344-1.344H80.124V5.375a2.69,2.69,0,0,0,2.687,2.687h4.031Z" transform="translate(-64)"/>
                                                  <path id="Path_1323" data-name="Path 1323" d="M143.452,298.667h-14.78a.672.672,0,1,0,0,1.344h14.78a.672.672,0,1,0,0-1.344Z" transform="translate(-123.969 -279.856)"/>
                                                  <path id="Path_1324" data-name="Path 1324" d="M143.452,234.667h-14.78a.672.672,0,1,0,0,1.344h14.78a.672.672,0,1,0,0-1.344Z" transform="translate(-123.969 -219.887)"/>
                                                  <path id="Path_1325" data-name="Path 1325" d="M143.452,362.667h-14.78a.672.672,0,1,0,0,1.344h14.78a.672.672,0,1,0,0-1.344Z" transform="translate(-123.969 -339.825)"/>
                                                  <path id="Path_1326" data-name="Path 1326" d="M136.734,426.667h-8.062a.672.672,0,0,0,0,1.344h8.062a.672.672,0,1,0,0-1.344Z" transform="translate(-123.969 -399.794)"/>
                                                  <path id="Path_1327" data-name="Path 1327" d="M234.667,171.338a.671.671,0,0,0,.672.672H243.4a.672.672,0,1,0,0-1.344h-8.062A.672.672,0,0,0,234.667,171.338Z" transform="translate(-223.918 -159.917)"/>
                                                  <path id="Path_1328" data-name="Path 1328" d="M130.015,80.711h-1.344a.672.672,0,0,0,0,1.344h.672a.672.672,0,1,0,1.344,0v-.124a2.011,2.011,0,0,0-.672-3.907.672.672,0,1,1,0-1.344h1.344a.672.672,0,1,0,0-1.344h-.672a.672.672,0,0,0-1.344,0v.124a2.011,2.011,0,0,0,.672,3.907.672.672,0,1,1,0,1.344Z" transform="translate(-123.969 -69.962)"/>
                                                </g>
                                              </g>
                                            </g>
                                          </svg>
                                    </div>
                                    <div class="right">Fatura të rregullta për cdo blerje.</div>
                                </div>
                                <div class="col-row">
                                    <div class="left">
                                        <svg id="surface1" xmlns="http://www.w3.org/2000/svg" width="27.28" height="27.28" viewBox="0 0 27.28 27.28">
                                            <path id="Path_1329" data-name="Path 1329" d="M22.734,4.817V2.273A2.276,2.276,0,0,0,20.46,0H2.273A2.276,2.276,0,0,0,0,2.273v10a2.269,2.269,0,0,0,2.039,2.25l1.709,4.437a2.274,2.274,0,0,0,2.939,1.3l10.673-4.111a6.573,6.573,0,0,0,.566,5.836l.261.418v4.417a.455.455,0,0,0,.455.455h8.184a.455.455,0,0,0,.455-.455V11.817a5.95,5.95,0,0,0-1.742-4.191Zm0,2.719,1.38,3.582a1.364,1.364,0,0,1-.783,1.763l-.789.3a2.274,2.274,0,0,0,.192-.909ZM.909,12.276v-10A1.364,1.364,0,0,1,2.273.909H20.46a1.364,1.364,0,0,1,1.364,1.364v2.89h0l0,.01v7.1a1.383,1.383,0,0,1-.031.236L17.629,8.346a2.426,2.426,0,0,0-3.5,3.354L15.9,13.64H2.273A1.364,1.364,0,0,1,.909,12.276Zm5.825,2.273L3.5,15.794l-.478-1.245Zm-.375,4.869A1.364,1.364,0,0,1,4.6,18.636l-.767-1.995,5.427-2.091v0h7.48l.591.644Zm20.011,6.953H19.1V22.734h7.275Zm0-4.547H18.894l-.2-.315a5.67,5.67,0,0,1-.115-5.823.455.455,0,0,0-.06-.533L14.8,11.089a1.516,1.516,0,0,1,2.189-2.1l7.245,7.244.643-.643L23.191,13.91l.467-.182a2.274,2.274,0,0,0,1.3-2.937l-1.539-4L24.9,8.27a5.035,5.035,0,0,1,1.476,3.547Zm0,0"/>
                                            <path id="Path_1330" data-name="Path 1330" d="M65.865,67.047a1.182,1.182,0,0,0,1.182-1.182V63.682A1.182,1.182,0,0,0,65.865,62.5H63.682A1.182,1.182,0,0,0,62.5,63.682v2.182a1.182,1.182,0,0,0,1.182,1.182Zm-2.455-1.182v-.637h.909v-.909h-.909v-.637a.273.273,0,0,1,.273-.273h2.182a.273.273,0,0,1,.273.273v.637h-.909v.909h.909v.637a.273.273,0,0,1-.273.273H63.682A.273.273,0,0,1,63.409,65.865Zm0,0" transform="translate(-60.681 -60.681)"/>
                                            <path id="Path_1331" data-name="Path 1331" d="M78.125,265.625h1.819v.909H78.125Zm0,0" transform="translate(-75.852 -257.896)"/>
                                            <path id="Path_1332" data-name="Path 1332" d="M78.125,359.375h1.819v.909H78.125Zm0,0" transform="translate(-75.852 -348.918)"/>
                                            <path id="Path_1333" data-name="Path 1333" d="M359.375,359.375h1.819v.909h-1.819Zm0,0" transform="translate(-348.918 -348.918)"/>
                                            <path id="Path_1334" data-name="Path 1334" d="M171.875,265.625h1.819v.909h-1.819Zm0,0" transform="translate(-166.874 -257.896)"/>
                                            <path id="Path_1335" data-name="Path 1335" d="M265.625,265.625h1.819v.909h-1.819Zm0,0" transform="translate(-257.896 -257.896)"/>
                                            <path id="Path_1336" data-name="Path 1336" d="M359.375,265.625h1.819v.909h-1.819Zm0,0" transform="translate(-348.918 -257.896)"/>
                                            <path id="Path_1337" data-name="Path 1337" d="M656.25,78.125h.909v1.364h-.909Zm0,0" transform="translate(-637.154 -75.852)"/>
                                            <path id="Path_1338" data-name="Path 1338" d="M593.75,78.125h.909v1.364h-.909Zm0,0" transform="translate(-576.472 -75.852)"/>
                                            <path id="Path_1339" data-name="Path 1339" d="M531.25,78.125h.909v1.364h-.909Zm0,0" transform="translate(-515.791 -75.852)"/>
                                            <path id="Path_1340" data-name="Path 1340" d="M468.75,78.125h.909v1.364h-.909Zm0,0" transform="translate(-455.11 -75.852)"/>
                                            <path id="Path_1341" data-name="Path 1341" d="M687.5,812.5h.909v.909H687.5Zm0,0" transform="translate(-667.494 -788.857)"/>
                                          </svg>
                                    </div>
                                    <div class="right">Paguaj online apo me para në dorë kur të pranoni porosinë.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="products-vendor">
                <div class="divider"></div>
                <div class="title">Produkte nga dyqani</div>
                <div class="divider"></div>
                <div class="product-list swiper-container similarSwiper">
                    <div class="swiper-wrapper">
                        @php
                            $isLoggedIn = false;
                            if(current_user()){
                                $isLoggedIn = current_user()->id;
                            }
                        @endphp
                        @foreach($product->owner->products()->where('id', '!=', $product->id)->take(10)->get() as $similar)
                        @php
                            if($similar->minoffers() && $similar->minoffers()->discount != 0){
                                $offer = $similar->minoffers();
                                if($offer->type < 3){
                                    $offerPrice = round($similar->price - (($similar->price * $offer->discount)/100), 2);
                                    $offerDiscount = '-'.round($offer->discount, 1).'%';
                                } else {
                                    $offerPrice = $offer->discount;
                                    $offerDiscount = '-'.($similar->price - $offer->discount).'€';
                                }
                            } else {
                                $offer = '';
                            }
                            $productLink = route('single.product', [$similar->owner->slug, $similar->id]);
                        @endphp
                        <div class="product swiper-slide">
                            <a href="{{ $productLink }}">
                                <div class="product_image">
                                    @if($offer)
                                        <div class="product-offer">{{ $offerDiscount }}</div>
                                    @endif
                                    @if($similar->image)
                                        @if(file_exists(public_path('/photos/products/230/'.$similar->image)))
                                            <img src="{{ asset('/photos/products/230/'.$similar->image) }}" class="swiper-lazy" />
                                        @else
                                            <img src="{{ asset('/photos/products/'.$similar->image) }}" class="swiper-lazy" />
                                        @endif
                                        <div class="swiper-lazy-preloader"></div>
                                    @endif
                                </div>
                            </a>
                            <div class="product_info">
                                <a href="{{ $productLink }}">
                                    <div class="product_info_title">{{$similar->name}}</div>
                                </a>
                                <div class="product_info_vendor">
                                    <a href="{{ route('single.vendor', $similar->owner->slug) }}">
                                        {{$similar->owner->name}}
                                        @if($similar->owner->verified)
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
                                <div class="product_info_price">
                                    @if($offer)
                                        <div class="current_price">{{ number_format($offerPrice,2) }}€</div>
                                        <div class="old_price">{{ number_format($similar->price,2) }}€</div>
                                    @else
                                        <div class="current_price">{{ number_format($similar->price,2) }}€</div>
                                    @endif
                                </div>
                                <livewire:products.add-to-cart :product="$similar" :isLoggedIn="$isLoggedIn" :plink="$productLink" :wire:key="'s-'.$similar->id">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="product-similar">
                <div class="divider"></div>
                <div class="title">Produkte të ngjashme</div>
                <div class="divider"></div>
                <div class="product-list">
                    @php
                        $isLoggedIn = false;
                        if(current_user()){
                            $isLoggedIn = current_user()->id;
                        }
                    @endphp
                    @foreach($product->similarProducts() as $similar)
                    @php
                        if($similar->minoffers() && $similar->minoffers()->discount != 0){
                            $offer = $similar->minoffers();
                            if($offer->type < 3){
                                $offerPrice = round($similar->price - (($similar->price * $offer->discount)/100), 2);
                                $offerDiscount = '-'.round($offer->discount, 1).'%';
                            } else {
                                $offerPrice = $offer->discount;
                                $offerDiscount = '-'.($similar->price - $offer->discount).'€';
                            }
                        } else {
                            $offer = '';
                        }
                        $productLink = route('single.product', [$similar->owner->slug, $similar->id]);
                    @endphp
                    <div class="product swiper-slide">
                        <a href="{{ $productLink }}">
                            <div class="product_image">
                                @if($offer)
                                    <div class="product-offer">{{ $offerDiscount }}</div>
                                @endif
                                @if($similar->image)
                                    @if(file_exists(public_path('/photos/products/230/'.$similar->image)))
                                        <img src="{{ asset('/photos/products/230/'.$similar->image) }}" class="swiper-lazy" />
                                    @else
                                        <img src="{{ asset('/photos/products/'.$similar->image) }}" class="swiper-lazy" />
                                    @endif
                                @endif
                            </div>
                        </a>
                        <div class="product_info">
                            <a href="{{ $productLink }}">
                                <div class="product_info_title">{{$similar->name}}</div>
                            </a>
                            <div class="product_info_vendor">
                                <a href="{{ route('single.vendor', $similar->owner->slug) }}">
                                    {{$similar->owner->name}}
                                    @if($similar->owner->verified)
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
                            <div class="product_info_price">
                                @if($offer)
                                    <div class="current_price">{{ number_format($offerPrice,2) }}€</div>
                                    <div class="old_price">{{ number_format($similar->price,2) }}€</div>
                                @else
                                    <div class="current_price">{{ number_format($similar->price,2) }}€</div>
                                @endif
                            </div>
                            <livewire:products.add-to-cart :product="$similar" :isLoggedIn="$isLoggedIn" :plink="$productLink" :wire:key="'s-'.$similar->id">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="divider"></div>
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
        <div class="sticky-qrcode">
            <div class="app-qrcode">
                <div class="close-qr"><i class="icon-times"></i></div>
                <a href="#"><img src="{{ asset('images/qrcode-elefandiapp.png') }}" alt="elefandi application"></a>
            </div>
        </div>
    </div>
    @livewire('products.vendor-chat', ['vendor'=>$product->owner, 'pid'=>$product->id])
</x-app-layout>