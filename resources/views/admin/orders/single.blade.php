<x-admin-layout>
    @push('scripts')
        <script src="{{ asset('js/print.js') }}"></script>
    @endpush
    @push('styles')
<style>
.orders-view {
    padding: 20px;
}
.orders-view .vendor-order {
    border-radius: .25rem;
    box-shadow: 1px 2px 10px #22222221;
    padding: 7px;
    margin-bottom: 15px;
}
.orders-view .vendor-order .vendor-info {
    display: flex;
}
.orders-view .vendor-order .vendor-info > div {
    flex: 0 0 auto;
}
.orders-view .vendor-order .vendor-info > .name {
    flex: 1 1 auto;
}
.orders-view .vendor-order .vendor-info .transport {
    margin-right: 20px;
}
.orders-view .vendor-order .table tr {
    text-align: left;
}
.orders-view .vendor-order .table tr th:not(:first-child), .orders-view .vendor-order .table tr td:not(:first-child){
    width: 100px;
    text-align: center;
}
.orders-view .order-info {
    margin-bottom: 30px;
    padding: 10px 15px;
    background-color: #f1f1f1;
    border: 1px solid #bfbfbf !important;
    font-size: 15px;
}
.orders-view .order-info > div {
    margin-bottom: 5px;
}
.orders-view .order-info .right {
    font-weight: 400;
}
.orders-view .order-info .total {
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 10px;
}
.orders-view .order-info .total .right {
    color: #ff0000;
}
.order-print {
    margin-top: 20px;
    color: #888;
    cursor: pointer;
    margin: 0 20px 10px;
}
.tworows {
    display: flex;
}
.tworows .left {
    flex: 0 1 auto;
    width: 50%;
}
.tworows .right {
    flex: 0 1 auto;
    width: 50%;
    text-align: right;
}
.order-status {
    display: flex;
    flex: 0 0 auto;
}
.order-status .orders-information {
    flex: 1 1 auto;
    display: flex;
    align-items: flex-end;
    flex-wrap: wrap;
    margin-right: 20px;
}
.order-status .orders-information .mark, .order-status .orders-information mark {
    padding: 0 4px;
    margin: 0 3px;
}
</style>
        <link rel="stylesheet" href="{{ asset('css/print.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Të gjitha Porositë</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.orders.index')}}">Porositë</a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Detajet e porosisë</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
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
                    <div class="order-status">
                        <p class="orders-information">Porosia <mark id="order-number">#{{ $order->id }}</mark> është regjistuar më daten: <mark id="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</mark> dhe statusi është: <mark id="order-status"><span class="statusc v{{$order->status}}">{{ $orderStatus }}</span></mark>.</p>
                    </div>
                    <h3>Detajet e porosisë</h3>
                    @if($order->notes)<p class="order-notes">Informata shtesë: {{ $order->notes }}</p>@endif
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
                                <div class="name">{!! ($vendor->vendor)? strtoupper($vendor->vendor->name) :'Nuk ekziston' !!}</div>
                                <div class="transport">Transport: <span>{{ number_format($vendor->transport,2) }}€</span></div>
                                <div class="price">Gjithsej: <span>{{ number_format($vendor->value,2) }}€</span></div>
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
                                            <td><a href="{{ ($vendor->vendor)?route('single.product', [$vendor->vendor->slug, $product->products->id]) : '#' }}">{{ $product->products->name }}</a></td>
                                            <td>{{ $product->qty }}</td>
                                            <td>{{ number_format($product->price,2) }}€</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="vendor-status">
                                <p class="orders-information">Statusi për këtë porosi nga Dyqani: {!! ($vendor->vendor)? strtoupper($vendor->vendor->name) :'Nuk ekziston' !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
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
                            @if($order->user)
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
                            @endif
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
            </div>
        </div>
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
</x-admin-layout>