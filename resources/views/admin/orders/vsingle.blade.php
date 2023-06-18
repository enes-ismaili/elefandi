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
    margin-bottom: 0;
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
    border: 1px solid #bfbfbf;
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
                        <p class="orders-information">Porosia <mark id="order-number">#{{ $order->order_id }}</mark> është regjistuar më daten: <mark id="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</mark> dhe statusi është: <mark id="order-status"><span class="statusc v{{$order->status}}">{{ $orderStatus }}</span></mark>.</p>
                        @livewire('order.order-status', ['oid' => $order->id, 'orderStatus'=>$order->status])
                    </div>
                    <h3>Detajet e porosisë</h3>
                    @if($order->order->notes)<p class="order-notes">Informata shtesë: {{ $order->order->notes }}</p>@endif
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produkte</th>
                                <th>Sasia</th>
                                <th>Shuma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $product)
                                <tr>
                                    <td><a href="{{ ($product->products->owner)?route('single.product', [$product->products->owner->slug, $product->products->id]):'#' }}">{{ $product->products->name }}</a></td>
                                    <td>{{ $product->qty }}</td>
                                    <td>{{ number_format($product->price,2) }}€</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                            @if($order->order->user)
                            <div class="col-6">
                                <h3>Adresa e porosisë</h3>
                                <div class="address-info">
                                    <div class="name">{{ $order->order->user->first_name.' '.$order->order->user->last_name }}</div>
                                    <div class="address">{{ $order->order->user->address }}</div>
                                    <div class="city">{{ $order->order->user->zipcode.', '.((is_numeric($order->order->user->city) && $order->order->user->country_id < 4)?$order->order->user->cities->name : $order->order->user->city) }}</div>
                                    <div class="state">{{ $order->order->user->country()->name }}</div>
                                    <div class="phone">{{ $order->order->user->phone }}</div>
                                    <div class="email">{{ $order->order->user->email }}</div>
                                </div>
                            </div>
                            @endif
                            @if($order->order->address)
                            <div class="col-6">
                                <h3>Adresa e dërgesës</h3>
                                <div class="address-info">
                                    <div class="name">{{ $order->order->address->name }}</div>
                                    <div class="address">{{ $order->order->address->address }}</div>
                                    @if($order->order->address->address2) <div class="address">{{ $order->order->address->address2 }}</div> @endif
                                    <div class="city">{{ $order->order->address->zipcode.', '.((is_numeric($order->order->address->city) && $order->order->address->country_id < 4)?$order->order->address->cityF->name : $order->order->address->city) }}</div>
                                    <div class="state">{{ $order->order->address->country->name }}</div>
                                    <div class="phone">{{ $order->order->address->phone }}</div>
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
            document.addEventListener("DOMContentLoaded", () => {
                let orderStatus = document.querySelector("#order-status");
                console.log('test')
                window.livewire.on('statusChange', (e) => {
                    console.log(e);
                    orderStatus.innerHTML = e;
                });
            });
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