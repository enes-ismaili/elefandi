<x-app-layout>
    @push('scripts')
<script>
    let copyDatas = document.querySelectorAll('.copydatas');
    let basicData = document.querySelector('.basicdata');
    let shippingData = document.querySelector('.shippingdata');
    copyDatas.forEach(copyData => {
        copyData.addEventListener('click', e => {
            let cfName = basicData.querySelector('#fname').value;
            let clName = basicData.querySelector('#lname').value;
            let cPhone = basicData.querySelector('#phone').value;
            let cAddress = basicData.querySelector('#address').value;
            shippingData.querySelector('#sname').value = cfName+' '+clName;
            shippingData.querySelector('#sphone').value = cPhone;
            shippingData.querySelector('#saddress').value = cAddress;
            console.log(e);
        })
    })
    let openLoginModals = document.querySelectorAll('.openLoginModal');
    openLoginModals.forEach(openLoginModal => {
        openLoginModal.addEventListener('click', e => {
            window.livewire.emitTo('header.login-user', 'openLogin');
        });
    })
    document.addEventListener("DOMContentLoaded", () => {
        window.livewire.on('change-countries', (e) => {
            window.history.back();
        });
    });
</script>
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', 'Përfundimi i blerjes')
    @php
        if(isset($_COOKIE['country_id']) && $_COOKIE['country_id']){
            $currentCountry = $_COOKIE['country_id'];
        } else {
            $currentCountry = 1;
        }
        $totalPrice = 0;
        $transPrice = 0;
        $isCorrect = true;
    @endphp
    <div class="container">
        <div class="checkout-page">
            <div class="page-title">Përfundimi i blerjes</div>
            <form class="checkout-main" action="{{ route('view.checkout.submit') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-7">
                        @if(!$isLoggedIn)
                        <p style="background: #f1f1f1; padding: 12px; color: black;"><i class="fas fa-sign-in-alt"></i> Jeni të regjistruar në dyqanin tonë? <span class="openLoginModal" >Klikoni këtu për tu kyqur.</span></p>
                        @endif
                        <div class="card basicdata">
                            <div class="card-header">
                                <h5>Të dhënat personale</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    if($isLoggedIn){
                                        if(!current_user()->first_name || !current_user()->phone || !current_user()->email || !current_user()->country_id || !current_user()->city || !current_user()->address){
                                            $isCorrect = false;
                                            echo '<div class="row">';
                                                echo '<div class="col-12">';
                                                    echo '<span class="text-danger error">Ju duhet të përfundoni plotësimin e profilit për të vazhduar me blerjen</span>';
                                                    echo '<br><a href="'.route('profile.edit').'"><b style="color: #f00;margin-bottom: 15px;display: block;">Shko tek Profili</b></a>';
                                                echo '</div>';
                                            echo '</div>';
                                        }
                                    }
                                @endphp
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="fname">Emri *</label>
                                            <input type="text" name="fname" class="form-control" id="fname" placeholder="Emri" @if($isLoggedIn) value="{{ current_user()->first_name }}" disabled @else value="{{ old('fname') }}" @endif>
                                            @error('fname') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="lname">Mbiemri *</label>
                                            <input type="text" name="lname" class="form-control" id="lname" placeholder="Mbiemri" @if($isLoggedIn) value="{{ current_user()->last_name }}" disabled @else value="{{ old('fname') }}" @endif>
                                            @error('lname') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="phone">Numri i telefonit *</label>
                                            <input type="text" name="phone" class="form-control" id="phone" placeholder="Telefoni" @if($isLoggedIn) value="{{ current_user()->phone }}" disabled @else value="{{ old('phone') }}" @endif>
                                            @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="email">Email Adresa *</label>
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Email" @if($isLoggedIn) value="{{ current_user()->email }}" disabled @else value="{{ old('email') }}" @endif>
                                            @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @livewire('user.select-countries', [
                                        'selCountry'=> ( ($isLoggedIn) ? ((current_user()->country_id) ? current_user()->country_id : 0) : ((old('country')) ? old('country') : 0) ), 
                                        'selCity'=>(($isLoggedIn) ? ((current_user()->city) ? current_user()->city : '') : ((old('city')) ? old('city') : '')), 
                                        'disabled'=> ($isLoggedIn ? true : false), 'cchangeState'=>false
                                    ])
                                    @error('country') <span class="text-danger error">{{ $message }}</span>@enderror
                                    @error('city') <span class="text-danger error">{{ $message }}</span>@enderror
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            <label for="country">Shteti *</label>
                                            <input type="text" name="country" class="form-control" id="country" placeholder="Shteti" @if($isLoggedIn) value="{{ current_user()->country()->name }}" disabled @endif>
                                            @error('country') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="zipcode">Qyteti *</label>
                                            <input type="text" name="city" class="form-control" id="city" placeholder="Qyteti" @if($isLoggedIn) value="{{ (is_numeric(current_user()->city))?current_user()->cities->name : current_user()->city }}" disabled @endif>
                                            @error('city') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="address">Adresa *</label>
                                            <input type="text" name="address" class="form-control" id="address" placeholder="Adresa" @if($isLoggedIn) value="{{ current_user()->address }}" disabled @else value="{{ old('address') }}" @endif>
                                            @error('address') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!$isLoggedIn)
                        <div class="card shippingdata">
                            <div class="card-header">
                                <h5>Adresa e dërgimit</h5>
                                <div class="right copydatas pointer" style="text-decoration: underline;">Kopjo nga "Të dhënat personale"</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="sname">Personi kontaktues *</label>
                                            <input type="text" name="sname" class="form-control" id="sname" placeholder="Emri" value="{{ old('sname') }}">
                                            @error('sname') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="sphone">Numri i telefonit *</label>
                                            <input type="text" name="sphone" class="form-control" id="sphone" placeholder="Telefoni" value="{{ old('sphone') }}">
                                            @error('sphone') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @livewire('user.select-countries', [
                                        'selCountry'=> $currentCountry, 
                                        'selCity'=>(old('scity') ? old('scity') : ''), 
                                        'countyField' => 'scountry', 'cityField' => 'scity', 'cdisabled'=>true
                                    ])
                                    @error('scountry') <span class="text-danger error">{{ $message }}</span>@enderror
                                    @error('scity') <span class="text-danger error">{{ $message }}</span>@enderror
                                    {{-- <div class="col-6">
                                        <div class="form-group">
                                            <label for="scountry">Shteti *</label>
                                            <input type="text" name="scountry" class="form-control" id="scountry" placeholder="Shteti">
                                            @error('scountry') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="scity">Qyteti *</label>
                                            <input type="text" name="scity" class="form-control" id="scity" placeholder="Kodi Postal">
                                            @error('scity') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="saddress">Adresa *</label>
                                            <input type="text" name="saddress" class="form-control" id="saddress" placeholder="Adresa" value="{{ old('saddress') }}">
                                            @error('saddress') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="card">
                                <div class="card-header">
                                    <h5>Zgjidh Adresën</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($address as $saddress)
                                        <div class="form-group">
                                            <input type="radio" name="addresses" id="adr{{$saddress->id}}" value="{{$saddress->id}}" @if($saddress->primary || (count($address) == 1)) checked @endif required>
                                            <label for="adr{{$saddress->id}}">{{ $saddress->name.', '.$saddress->address.', '.((is_numeric($saddress->city) && $saddress->country_id < 4)?$saddress->cityF->name:$saddress->city) }}</label>
                                        </div>
                                    @endforeach
                                    @error('addresses') <span class="text-danger error">{{ $message }}</span>@enderror
                                    @livewire('user.change-address', ['type'=>'add', 'addressState'=>$currentCountry, 'changeState'=>true, 'needReload'=>false, 'cchangeState'=>false])
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-5">
                        <div class="card">
                            <div class="card-header">
                                <h5>Informata shtesë</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea class="form-control" name="additionalinformation" id="additionalinformation" rows="3" placeholder="Ju lutem shkruani të gjitha informatat shtesë në lidhje me këtë porosi."></textarea>
                                    @error('additionalinformation') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        @if($isLoggedIn)
                        <div class="card checkout-total">
                            <div class="card-header">
                                <h5>Porosia juaj</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produkte</th>
                                            <th>Sasia</th>
                                            <th>Çmimi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $hasCoupon = false;
                                            $couponDisscountPrice = 0;
                                            if (isset($_COOKIE['couponCode'])) {
                                                $couponCode = $_COOKIE['couponCode'];
                                                if($couponCode) {
                                                    $coupon = App\Models\Coupon::where('code', '=', $couponCode)->first();
                                                    if($coupon){
                                                        $today = time();
                                                        $startDate = strtotime($coupon->start_date);
                                                        $expireDate = strtotime($coupon->expire_date);
                                                        if($today > $startDate && $today < $expireDate){
                                                            // return $this->addCoupon();
                                                            $hasCoupon = true;
                                                            if($coupon->type == 1){
                                                            } elseif($coupon->type == 2){
                                                                $categoryList = '';
                                                                $i = 0;
                                                                foreach(json_decode($coupon->categories) as $category){
                                                                    $i++;
                                                                    $couponArray[] = $category;
                                                                    $thisCategory = App\Models\Category::where('id', '=', $category)->first();
                                                                    if($i > 1){
                                                                        $categoryList .= ', ';
                                                                    }
                                                                    $categoryList .= $thisCategory->name;
                                                                }
                                                            } else {
                                                                $i = 0;
                                                                foreach(json_decode($coupon->products) as $product){
                                                                    $i++;
                                                                    $couponArray[] = $product;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        @php
                                            $allProducts = [];
                                            $vendorProducts = [];
                                            foreach($currentCart as $product){
                                                $currProduct = $product->product;
                                                $vendorProducts[$currProduct->vendor_id][] = $product;
                                            }
                                        @endphp
                                        @foreach($vendorProducts as $cvendors)
                                        @php
                                            $transTextD = '';
                                            $transportMax = 0;
                                            $vtransPrice = 0;
                                            $vtotalPrice = 0;
                                        @endphp
                                        @foreach($cvendors as $product)
                                            @php
                                                $currProduct = $product->product;
                                            @endphp
											@if($product->qty && $product->qty > 0 && $currProduct->status == 1 && $currProduct->vstatus == 1)
                                            @php
                                                $currentVariant = $currProduct->cartVariant($product->variant_id);
                                                $noStock = false;
                                                $hasOffer = false;
                                                if($currentVariant){
                                                    $prodPrice = $currentVariant->price ? $currentVariant->price : $currProduct->price;
                                                    $oldPrice = $prodPrice;
                                                    if($currProduct->offers($product->variant_id)){
                                                        $offer = $currProduct->offers($product->variant_id);
                                                        if($offer->type < 3){
                                                            $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                                                        } else {
                                                            $prodPrice = $offer->discount;
                                                        }
                                                        $hasOffer= true;
                                                    } else {
                                                        $offer = '';
                                                    }
                                                    $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $currProduct->stock;
                                                    $productStock = $product->qty;
                                                    if($prodMaxStock){
                                                        if($product->qty > $prodMaxStock){
                                                            $productStock = $prodMaxStock;
                                                        }
                                                    } else {
                                                        $noStock = true;
                                                    }
                                                    $varName = $currentVariant->name;
                                                } else {
                                                    $prodPrice = $currProduct->price;
                                                    $oldPrice = $prodPrice;
                                                    if($currProduct->offers()){
                                                        $offer = $currProduct->offers();
                                                        if($offer->type < 3){
                                                            $prodPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 3);
                                                        } else {
                                                            $prodPrice = $offer->discount;
                                                        }
                                                        $hasOffer= true;
                                                    } else {
                                                        $offer = '';
                                                    }
                                                    $prodMaxStock = $currProduct->stock;
                                                    $productStock = $product->qty;
                                                    if($prodMaxStock){
                                                        if($product->qty > $prodMaxStock){
                                                            $productStock = $prodMaxStock;
                                                        }
                                                    } else {
                                                        $productStock = 0;
                                                        $prodMaxStock = 0;
                                                        $noStock = true;
                                                    }
                                                    $varName = '';
                                                }
                                                $getShipping = $currProduct->shippings->where('country_id', '=', $currentCountry)->first();
                                                $shippingCTransport = false;
                                                if(!$currProduct->shipping && $getShipping){
                                                    if($getShipping->free){
                                                        $shippingCost = 0;
                                                    } else {
                                                        $shippingCost = $getShipping->cost;
                                                    }
                                                    if($getShipping->shipping == 1){
                                                        $shippingCTransport = true;
                                                    }
                                                } else {
                                                    $getShipping = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                                                    if($getShipping->cost){
                                                        $shippingCost = $getShipping->cost;
                                                    } else {
                                                        $shippingCost = 0;
                                                    }
                                                    if($getShipping->transport > 0){
                                                        $shippingCTransport = true;
                                                    }
                                                }
                                                if($hasCoupon && !$noStock && $getShipping && $shippingCTransport){
                                                    if($currProduct->owner->id ==  $coupon->vendor_id){
                                                        $couponDisscountPriceP = 0;
                                                        $calcCoupon = true;
                                                        if($hasOffer && $coupon->withoffer){
                                                            if($coupon->action == 2){
                                                                if($coupon->discount < $prodPrice){
                                                                    $couponPriceP = $prodPrice - $coupon->discount;
                                                                    $couponDisscountPriceP = $coupon->discount;
                                                                } else {
                                                                    $couponPriceP = $prodPrice;
                                                                    $couponDisscountPriceP = 0;
                                                                    $calcCoupon = false;
                                                                }
                                                            } else {
                                                                $couponPriceP = ($prodPrice * (100 - $coupon->discount))/100;
                                                                $couponDisscountPriceP = $prodPrice - $couponPriceP;
                                                            }
                                                        } else {
                                                            if($coupon->action == 2){
                                                                if($coupon->discount < $oldPrice){
                                                                    $couponPriceP = $oldPrice - $coupon->discount;
                                                                    $couponDisscountPriceP = $coupon->discount;
                                                                } else {
                                                                    $couponPriceP = $oldPrice;
                                                                    $couponDisscountPriceP = 0;
                                                                    $calcCoupon = false;
                                                                }
                                                            } else {
                                                                $couponPriceP = ($oldPrice * (100 - $coupon->discount))/100;
                                                                $couponDisscountPriceP = $oldPrice - $couponPriceP;
                                                            }
                                                        }
                                                        if($calcCoupon){
                                                            if($coupon->type == 1){
                                                                $couponPrice = $couponPriceP;
                                                                $couponDisscountPrice += $couponDisscountPriceP;
                                                                $hasCouponActive = true;
                                                                $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                                            } else if($coupon->type == 2){
                                                                $productCategories = $currProduct->allCategories()->pluck('id')->toArray();
                                                                $hasCategory = array_intersect($productCategories, $couponArray);
                                                                if(count($hasCategory)){
                                                                    $couponPrice = $couponPriceP;
                                                                    $couponDisscountPrice += $couponDisscountPriceP;
                                                                    $hasCouponActive = true;
                                                                    $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                                                }
                                                            } else {
                                                                if(in_array($currProduct->id, $couponArray)){
                                                                    $couponPrice = $couponPriceP;
                                                                    $couponDisscountPrice += $couponDisscountPriceP;
                                                                    $hasCouponActive = true;
                                                                    $couponDisscount = $coupon->discount.(($coupon->action == 1) ? '%' : '€');
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                if(!$noStock && $getShipping && $shippingCTransport){
                                                    $totalPrice += ($product->qty * $prodPrice);
                                                    $vtotalPrice += ($product->qty * $prodPrice);
                                                    // $transPrice += $shippingCost;
                                                    $vtransPrice += $shippingCost;
                                                    if($transportMax < $shippingCost){
                                                        $transportMax = $shippingCost;
                                                    }
                                                }
                                            @endphp
                                            @if(!$noStock && $getShipping && $shippingCTransport)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">{{$currProduct->name}}</a>
                                                        @if($varName)
                                                            <br>
                                                            <span>Varianti: <span>{{ $currentVariant->name }}</span></span>
                                                        @endif
                                                        @if(isset($product->personalize) && $product->personalize)
                                                            <br>
                                                            <span>Personalizim: <span>{{ $product->personalize }}</span></span>
                                                        @endif
                                                        <br>
                                                        <span>Dyqani: 
                                                            <a href="{{ route('single.vendor', $currProduct->owner->slug) }}">{!! strtoupper($currProduct->owner->name) !!}
                                                                @if($currProduct->owner->verified)<img class="vendor-verification" title="Dyqan i verifikuar" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">@endif
                                                            </a>
                                                        </span>
                                                    </td>
                                                    <td>{{ $product->qty }}</td>
                                                    <td>{{ number_format($prodPrice,2) }}€</td>
                                                </tr>
                                            @endif
											@endif
                                        @endforeach
                                        @php
                                            $getShippingVendor = $currProduct->owner->shippings()->where('country_id', '=', $currentCountry)->first();
                                            $transType = 1;
                                            if($getShippingVendor){
                                                $transType = $getShippingVendor->transport;
                                            }
                                            if($getShippingVendor){
                                                if($transType == 2){
                                                    if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                                        $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                                                    } else {
                                                        $transPrice += $vtransPrice;
                                                    }
                                                } else if($transType == 3){
                                                    $transPrice += $transportMax;
                                                    $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                                                } else if($transType == 4){
                                                    $transTextD = $currProduct->owner->name.' ofron paguaj transportin më të madh të një prej produkteve dhe për pjesën tjetër falas';
                                                    if($getShippingVendor->limit && $vtotalPrice >= $getShippingVendor->limit) {
                                                        $transTextD = $currProduct->owner->name.' ofron transport falas mbi '.$getShippingVendor->limit.' €';
                                                    } else {
                                                        $transPrice += $transportMax;
                                                    }
                                                } else {
                                                    $transPrice += $vtransPrice;
                                                }
                                            }
                                        @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="divider"></div>
                                <div class="tworows subtotal">
                                    <div class="left">Nëntotal</div>
                                    <div class="right"><span>{{ number_format($totalPrice,2) }}</span> €</div>
                                </div>
                                <div class="tworows subtotal">
                                    <div class="left">Kosto e transportit</div>
                                    <div class="right">@if($transPrice)<span>{{ number_format($transPrice,2) }}</span> €@else Falas @endif</div>
                                </div>
                                @if($couponDisscountPrice)
                                <div class="tworows subtotal">
                                    <div class="left">Kuponi</div>
                                    <div class="right"><span>- {{ number_format($couponDisscountPrice,2) }}</span> €</div>
                                </div>
                                @endif
                                <div class="tworows subtotal">
                                    <div class="left">Total</div>
                                    <div class="right"><span>{{ number_format(($totalPrice + $transPrice - $couponDisscountPrice),2) }}</span> €</div>
                                </div>
                                <div class="divider"></div>
                                <div class="payment-options">
                                    <label for="">Metoda e Pagesës</label>
                                    <div>PARA NË DORË</div>
                                    <div>Paguaj pasi ta pranoni porosinë</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <input name="terms-and-conditions" class="form-control" type="checkbox" id="termsandconditions" required="">
                                <label for="termsandconditions">Unë i pranoj Kushtet e Përdorimit dhe Politikat e Privatësisë</label>
                            </div>
                            <p id="termserror" style="color:red; display:none;"><i class="fa fa-exclamation-circle"></i> Ju lutem pranoni kushtet e përdorimit për të vazhduar!</p>
                        </div>
                        @if($totalPrice && $totalPrice >= 0 && $isCorrect)
                            <button type="submit" class="btn full-width" >Përfundo blerjen</button>
                        @else
                            <button type="submit" class="btn c5 full-width" disabled >Përfundo blerjen</button>
                        @endif
                        @else
                            @livewire('cart.view-checkout')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>