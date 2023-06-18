<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.single-tracking {
    display: flex;
    margin-bottom: 5px;
    padding: 5px 10px;
    background-color: #f1f1f1;
    font-size: 15px;
}
.single-tracking .date {
    flex: 0 0 auto;
    width: 135px;
    border-right: 1px solid #ddd;
    margin-right: 10px;
}
</style>
    @endpush
    @section('pageTitle', 'Rezultatet e Gjurmimit')
    <div class="container">
        @if($orderTrackSearch)
            <h1 class="tcenter">Rezultatet e gjurmimit</h1>
            @if($error)
                <p class="page-information tcenter">Të dhënat janë gabim. Plotësoni të dhënat me kujdes.</p>
                <div class="row">
                    <a href="{{ route('view.track') }}" class="btn center">Gjurmim i ri</a>
                </div>
            @else
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
                <p class="orders-information tcenter">Porosia <mark id="order-number">#{{ $order->id }}</mark> është regjistuar më daten: <mark id="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</mark> dhe statusi është: <mark id="order-status"><span class="statusc v{{$order->status}}">{{ $orderStatus }}</span></mark>.</p>
                @foreach($orderVendor as $vendor)
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
                    <div class="tracking">
                        <h4>Informacion rreth Gjurmimit të porosisë nga dyqani <a href="{{ route('single.vendor', $vendor->vendor->slug) }}">{!! strtoupper($vendor->vendor->name) !!}</a></h4>
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
                                <h5>Ende nuk është përditësuar gjurmimi për këtë porosi, ju lutem kontaktoni me dyqanin.</h5>
                            </div>
                        @endif
                        </div>
                        <div class="vendor-status">
                            <p class="orders-information">Statusi për këtë porosi për produktet nga Dyqani: {!! strtoupper($vendor->vendor->name) !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
                        </div>
                    </div>
                @endforeach
                <div class="row">
                    <a href="{{ route('view.track') }}" class="btn center">Gjurmim i ri</a>
                </div>
            @endif
        @else
            <h1 class="tcenter">Gjurmo Porosinë</h1>
            <p class="page-information tcenter">Për të gjurmuar porosinë, ju lutem shkruani numrin e porosisë, email adresën dhe shtypni butonin "Gjurmo". Këto të dhëna i keni pranuar në mesazhin e konfirimit të porosisë në email adresën tuaj.</p>
            <form action="{{ route('view.track.post') }}" method="post" class="order-tracking">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="ordernumber">Numri i Porosisë</label>
                            <input type="text" class="form-control" name="ordernumber" id="ordernumber" placeholder="Shkruani numrin e porosisë">
                            @error('ordernumber') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email Adresa</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Shkruani email adresën">
                            @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <button class="btn">Gjurmo Porosisë</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>