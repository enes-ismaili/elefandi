<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
<style>

</style>
    @endpush
    @section('pageTitle', 'Gjurmimi i Porosisë')
    <div class="container">
        <div class="site-main profile single-order">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                <h1 class="profile-title">Gjurmimi i Porosisë <a href="{{ route('profile.orders.single', $order->id) }}">#{{ $order->id }}</a></h1>
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
                                    {!! strtoupper($vendor->vendor->name) !!}
                                    @if($vendor->vendor->verified)
                                        <img title="Dyqan i verifikuar" class="vendor-verification" alt="Dyqan i verifikuar" src="{{asset('/images/verified.png')}}">
                                    @endif
                                </div>
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
                                <h4>Informacion rreth Gjurmimit të porosisë nga ky dyqan</h4>
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
                                        <h5>Ende nuk është përditësuar statusi për këtë porosi, ju lutem kontaktoni me dyqanin.</h5>
                                    </div>
                                @endif
                                </div>
                            </div>
                            <div class="vendor-status">
                                <p class="orders-information">Statusi për këtë porosi nga Dyqani: {!! strtoupper($vendor->vendor->name) !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </main>
        </div>
    </div>
</x-app-layout>