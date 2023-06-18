<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $vendor->name)
    <div class="container">
        <div class="single-vendor">
            <div class="vendor-cover"><img src="{{ asset('photos/cover/'.$vendor->cover_path) }}" alt=""></div>
            <div class="vendor">
                <div class="left">
                    <div class="vendor-logo">
                        <img src="{{ asset('photos/vendor/'.$vendor->logo_path) }}" alt="">
                    </div>
                    <div class="card">
                        {{-- //TODO Get rating --}}
                        @php
                        $ratingsAll = $vendor->products()->whereHas('ratings')->select('id')->with('ratings:rating,product_id')->get();
                        if($ratingsAll){
                            $ratingAverage = $ratingsAll->pluck('ratings')->flatten()->pluck('rating')->avg();
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
                        <div class="vendor-name">{!! $vendor->name !!} @if($vendor->verified)<img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">@endif</div>
                        <div class="vendor-rating">
                            <div class="ratings r{{ $ratingAverageRoundS }}">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div class="divider"></div>
                        <div class="vendor-desc">{{ $vendor->description }}</div>
                        <div class="divider"></div>
                        <div class="vendor-category">
                            <ul>
                                @foreach ($vendorCategories as $category)
                                    @if($category)
                                    <li>
                                        <a href="{{ route('category.single', $category->slug) }}">{{ $category->name }}</a>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <div class="divider"></div>
                        <div class="vendor-workday">
                            @if($vendor->workhour)
                                @php
                                    $vendorWork = $vendor->workhour;
                                @endphp
                                <div class="title">Orari i punes se dyqanit fizik</div>
                                <ul>
                                    <li><span>E Hënë</span>{{ ($vendorWork->monday) ? \Carbon\Carbon::parse($vendorWork->monday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->monday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Martë</span>{{ ($vendorWork->tuesday) ? \Carbon\Carbon::parse($vendorWork->tuesday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->tuesday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Mërrkurë</span>{{ ($vendorWork->wednesday) ? \Carbon\Carbon::parse($vendorWork->wednesday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->wednesday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Enjte</span>{{ ($vendorWork->thursday) ? \Carbon\Carbon::parse($vendorWork->thursday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->thursday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Premte</span>{{ ($vendorWork->friday) ? \Carbon\Carbon::parse($vendorWork->friday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->friday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Shtunë</span>{{ ($vendorWork->saturday) ? \Carbon\Carbon::parse($vendorWork->saturday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->saturday_end)->format('H:i') : 'Pushim' }}</li>
                                    <li><span>E Dielë</span>{{ ($vendorWork->sunday) ? \Carbon\Carbon::parse($vendorWork->sunday_start)->format('H:i').'-'.\Carbon\Carbon::parse($vendorWork->sunday_end)->format('H:i') : 'Pushim' }}</li>
                                </ul>
                            @endif
                        </div>
                        <div class="divider"></div>
                        <div class="vendor-qrcode">
                            <div class="title">Shiko Dyqanin në Telefon</div>
                            <div class="stitle">Skano kodin dhe hap dyqanin</div>
                            <div class="qr-code"><img src="{{ asset('photos/qrcodes/vendor/'.$vendor->qrcode) }}"></div>
                            <div class="btitle">Shpejt dhe lehtë!</div>
                        </div>
                    </div>
                </div>
                <div class="main">
                    <div class="card">
                        <div class="row top-info">
                            <div class="col-4">
                                <div class="title">Na ndiqni në rrjetet sociale</div>
                                <div class="divider"></div>
                                @php 
                                //print_r($vendor->socials); 
                                @endphp
                                <ul class="social-links">
                                    @foreach($vendor->socials as $social)
                                        @php
                                            $sExist = false;
                                            if($social->name == 'facebook'){
                                                $sClassName = 'fb';
                                                $sClassIcon = 'fab fa-facebook-f';
                                                $sExist = true;
                                            } else if($social->name == 'twitter'){
                                                $sClassName = 'tw';
                                                $sClassIcon = 'fab fa-twitter';
                                                $sExist = true;
                                            } else if($social->name == 'instagram'){
                                                $sClassName = 'ig';
                                                $sClassIcon = 'fab fa-instagram';
                                                $sExist = true;
                                            } else if($social->name == 'youtube'){
                                                $sClassName = 'yt';
                                                $sClassIcon = 'fab fa-youtube';
                                                $sExist = true;
                                            }
                                            if(!$social->links){
                                                $sExist = false;
                                            }
                                        @endphp
                                        @if($sExist)
                                        <li><a class="{{ $sClassName }}" href="{{ $social->links }}"><i class="{{ $sClassIcon }}"></i></a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-4">
                                <div class="title">Adresa</div>
                                <div class="divider"></div>
                                <p><a target="_blank" href="https://maps.google.com/?q={{ $vendor->address.', '.((is_numeric($vendor->city) && $vendor->country_id < 4) ? $vendor->cities->name : $vendor->city).', '.$vendor->country()->name }}">{{ $vendor->address.', '.((is_numeric($vendor->city) && $vendor->country_id < 4) ? $vendor->cities->name : $vendor->city).', '.$vendor->country()->name }}</a></p>
                            </div>
                            <div class="col-4">
                                <div class="title">Na telefononi</div>
                                <div class="divider"></div>
                                <p><a href="tel:{{ $vendor->phone }}">{{ $vendor->phone }}</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="vendor-tabs tab-list">
                        <ul class="tab-header">
                            <li class="active"><span data-id="tab-1">Produktet</span></li>
                            <li><span data-id="tab-2">Kushtet e përdorimit</span></li>
                        </ul>
                        <div class="tabs">
                            <div class="tab active" id="tab-1">
                                @livewire('products.list-products-vendor', ['vendorid'=> $vendor->id])
                            </div>
                            <div class="tab" id="tab-2">
                                @if($vendor->pages)
                                    <div class="content tmce">
                                        {!! $vendor->pages->perdorimi !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewire('products.vendor-chat', ['vendor'=>$vendor, 'pid'=>0])
</x-app-layout>