<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
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
                        $orderStatus = 'Anulluar';
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
                                    <td>{{ $product->products->name }}</td>
                                    <td>{{ $product->qty }}</td>
                                    <td>{{ number_format($product->price,2) }}€</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tracking">
                    @if(count($order->tracking))
                    <h4>Informacion rreth Gjurmimit të porosisë</h4>
                        <div class="tracking-information">
                            @foreach($order->tracking as $tracking)
                            <div class="single-tracking">
                                <div class="date">{{ \Carbon\Carbon::parse($tracking->created_at)->format('d.m.Y H:i') }}</div>
                                <div class="comment">{{ $tracking->comment }}</div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('vendor.orders.track.add', $order->id) }}" method="post" class="add-tracking">
                        @csrf
                        <h5>Shto informacion rreth gjurmimint</h5>
                        <div class="form-group">
                            <label for="addtracking">Shtoni Informacion</label>
                            <textarea name="comment" id="addtracking" class="form-control"></textarea>
                            @error('comment') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Shto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            console.log('test')
            let orderStatus = document.querySelector("#order-status");
            window.livewire.on('statusChange', (e) => {
                console.log(e);
                orderStatus.innerHTML = e;
            });
        });
        // document.addEventListener("DOMContentLoaded", () => {
        //     console.log('test');
        //     document.addEventListener("livewire:load", function(event) {
        //         window.livewire.on('statusChange', () => {
        //             let selectStatus = document.querySelectorAll("#orderStatus");
        //             console.log(selectStatus[0].value);
        //             let newTextStatus = 'Duke Procesuar';
        //             if(selectStatus[0].value == 1) {
        //                 newTextStatus = 'Dërguar';
        //             } else if (selectStatus[0].value == 2) {
        //                 newTextStatus = 'Anulluar';
        //             }
        //             document.querySelector('#order-status .statusc').innerHTML = newTextStatus;
        //         });
        //     });
        // });
    </script>
</x-admin-layout>