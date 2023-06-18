<x-app-layout>
    @push('scripts')
        <script src="{{ asset('js/print.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
        <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    @endpush
    @section('pageTitle', 'Detajet e Porositë')
    <div class="container">
        <div class="site-main profile single-order">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                @php
                    $orderStatus = 'Duke Procesuar';
                    if($order->status == 1){
                        $orderStatus = 'Dërguar';
                    } else if($order->status == 2){
                        $orderStatus = 'Anulluar';
                    } else if($order->status == 3){
                        $orderStatus = 'Dërguar & Anulluar';
                    }
                    $orderPayment = 'Para në dorë';
                    if (Cache::has('settings')) {
                        $allSettings = Cache::get('asettings');
                    } else {
                        $allSettings = Cache::rememberForever('asettings', function () {
                            return App\Models\Setting::all()->pluck('value', 'name');
                        });
                    }
                @endphp
                <div id="orders-view" class="orders-view">
                    <div class="only-print" style="display:none;"><img src="{{ asset('photos/images/'.$allSettings['invoice_logo']) }}" alt="123" title="123"></div>
                    <p class="orders-information">Porosia <mark id="order-number">#{{ $order->id }}</mark> është regjistuar më daten: <mark id="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</mark> dhe statusi është: <mark id="order-status"><span class="statusc v{{$order->status}}">{{ $orderStatus }}</span></mark>.</p>
                    @if($order->notes)<p class="order-notes">Informata shtesë: {{ $order->notes }}</p>@endif
                    <h3>Detajet e porosisë</h3>
                    @foreach($order->ordervendor as $vendor)
                        @php
                            $vOrderStatus = 'Duke Procesuar';
                            if($vendor->status == 1){
                                $vOrderStatus = 'Dërguar';
                            } else if($vendor->status == 2){
                                $vOrderStatus = 'Anulluar';
                            } else if($vendor->status == 3){
                                $vOrderStatus = 'Dërguar & Anulluar';
                            }
                        @endphp
                        <div class="vendor-order">
                            <div class="vendor-info">
                                <div class="name">
                                    {!! ($vendor->vendor)? strtoupper($vendor->vendor->name) : 'nuk ekziston' !!}
                                    @if($vendor->vendor && $vendor->vendor->verified)
                                        <img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">
                                    @endif
                                </div>
                                <div class="transport">Transport: <span>{{ number_format($vendor->transport, 2) }}€</span></div>
                                <div class="price">Gjithsej: <span>{{ number_format($vendor->value, 2) }}€</span></div>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produkte</th>
                                        <th>Sasia</th>
                                        <th>Shuma</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->details as $product)
                                        <tr>
                                            <td>{{ $product->products->name }}</td>
                                            <td>{{ $product->qty }}</td>
                                            <td>{{ number_format($product->price,2) }}€</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="vendor-status">
                                <p class="orders-information">Statusi për këtë porosi nga Dyqani: {!! ($vendor->vendor)? strtoupper($vendor->vendor->name) :'nuk ekziston' !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
                            </div>
                        </div>
                    @endforeach
                    <div class="card order-info">
                        <div class="tworows subtotal">
                            <div class="left">Nëntotal:</div>
                            <div class="right">{{ number_format($order->value,2) }}€</div>
                        </div>
                        <div class="tworows transport">
                            <div class="left">Shpenzimet e dërgesës:</div>
                            <div class="right">{{ number_format($order->transport,2) }}€</div>
                        </div>
                        <div class="tworows">
                            <div class="left">Metoda e pagesës:</div>
                            <div class="right">Para në dorë</div>
                        </div>
                        <div class="tworows total">
                            <div class="left">Gjithsej:</div>
                            <div class="right">{{ number_format(($order->value + $order->transport),2) }}€</div>
                        </div>
                    </div>
                    <div class="orders-address">
                        <div class="row">
                            <div class="col-6">
                                <h3>Adresa e porosisë</h3>
                                <div class="address-info">
                                    <div class="name">{{ $order->user->first_name.' '.$order->user->last_name }}</div>
                                    <div class="address">{{ $order->user->address }}</div>
                                    <div class="city">{{ $order->user->zipcode.', '.((is_numeric($order->user->city) && $order->user->country_id < 4)?$order->user->cities->name : $order->user->city) }}</div>
                                    <div class="state">{{ $order->user->country()->name }}</div>
                                    <div class="phone">{{ $order->user->phone }}</div>
                                    <div class="email">{{ $order->user->email }}</div>
                                </div>
                            </div>
                            @if($order->address)
                            <div class="col-6">
                                <h3>Adresa e dërgesës</h3>
                                <div class="address-info">
                                    <div class="name">{{ $order->address->name }}</div>
                                    <div class="address">{{ $order->address->address }}</div>
                                    @if($order->address->address2) <div class="address">{{ $order->address->address2 }}</div> @endif
                                    <div class="city">{{ $order->address->zipcode.', '.((is_numeric($order->address->city) && $order->address->country_id < 4)?$order->address->cityF->name : $order->address->city) }}</div>
                                    <div class="state">{{ $order->address->country->name }}</div>
                                    <div class="phone">{{ $order->address->phone }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="order-print"><i class="fas fa-print"></i> Printo Porosinë</div>
            </main>
            <script>
                let orderPrint = document.querySelector('.order-print');
                if(orderPrint){
                    orderPrint.addEventListener('click', e=> {
                        printJS(
                            {
                                printable: 'orders-view',
                                type: 'html',
                                targetStyles: ['*'],
                                css: ['{{ asset('css/print-order.css') }}'], 
                                style:`
                                    @page{size: auto;margin: 0;}
                                    #orders-view{margin:100px 30px 30px;}
                                    .only-print{display: block !important;position:absolute;top:15px;right:30px;}
                                    .only-print img{height: 50px;}
                                    .vendor-print{display: block !important;font-size:22px;font-weight:600;margin-bottom:20px;}
                                `
                            }
                        )
                    })
                }
            </script>
        </div>
    </div>
</x-app-layout>