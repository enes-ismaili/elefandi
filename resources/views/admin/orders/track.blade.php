<x-admin-layout>
    @push('scripts')
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
.order-status {
    display: flex;
    flex: 0 0 auto;
}
.order-status .orders-information {
    flex: 1 1 auto;
    display: flex;
    align-items: flex-end;
}
.order-status .orders-information .mark, .order-status .orders-information mark {
    padding: 0 4px;
    margin: 0 3px;
}
.tracking {
    padding: 5px;
}
.tracking-information {
    margin-bottom: 25px;
}
.single-tracking {
    display: flex;
}
.single-tracking .date {
    flex: 0 0 auto;
    width: 140px;
}
.single-tracking {
    display: flex;
    margin-bottom: 5px;
    padding: 5px 10px;
    background-color: #f1f1f1;
    font-size: 15px;
}
</style>
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
                <span>Gjurmimi i porosisë</span>
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
                @endphp
                <div id="orders-view" class="orders-view">
                    <p class="orders-information">Porosia <mark id="order-number">#{{ $order->id }}</mark> është regjistuar më daten: <mark id="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</mark> dhe statusi është: <mark id="order-status"><span class="statusc v{{$order->status}}">{{ $orderStatus }}</span></mark>.</p>
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
                                            <td>{{ $product->products->name }}</td>
                                            <td>{{ $product->qty }}</td>
                                            <td>{{ number_format($product->price,2) }}€</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="tracking">
                                <h6>Informacion rreth Gjurmimit të porosisë nga ky dyqan</h6>
                                <div class="tracking-information">
                                @if(count($vendor->tracking))
                                    @foreach($vendor->tracking as $tracking)
                                        <div class="single-tracking">
                                            <div class="date">{{ \Carbon\Carbon::parse($tracking->created_at)->format('d.m.Y H:i') }}</div>
                                            <div class="comment">{{ $tracking->comment }}</div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="single-tracking">
                                        <span>Ende nuk është përditësuar statusi për këtë porosi, ju lutem kontaktoni me dyqanin.</span>
                                    </div>
                                @endif
                                </div>
                            </div>
                            <div class="vendor-status">
                                <p class="orders-information">Statusi për këtë porosi nga Dyqani: {!! ($vendor->vendor)? strtoupper($vendor->vendor->name) :'Nuk ekziston' !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log('test');
            document.addEventListener("livewire:load", function(event) {
                window.livewire.on('statusChange', () => {
                    let selectStatus = document.querySelectorAll("#orderStatus");
                    console.log(selectStatus[0].value);
                    let newTextStatus = 'Duke Procesuar';
                    if(selectStatus[0].value == 1) {
                        newTextStatus = 'Dërguar';
                    } else if (selectStatus[0].value == 2) {
                        newTextStatus = 'Anulluar';
                    }
                    document.querySelector('#order-status .statusc').innerHTML = newTextStatus;
                });
            });
        });
    </script>
</x-admin-layout>