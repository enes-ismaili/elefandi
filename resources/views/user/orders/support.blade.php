<x-app-layout>
    @push('scripts')
        <script>
            let subjectChoose = document.querySelector('#subject_choose');
            let customSubject = document.querySelector('.custom-subject');
            if(subjectChoose){
                if(subjectChoose.value == 9){
                    customSubject.classList.add('show');
                }
                subjectChoose.addEventListener('change', e=>{
                    if(e.target.value == 9){
                        customSubject.classList.add('show');
                    } else {
                        if(customSubject.classList.contains('show')){
                            customSubject.classList.remove('show');
                        }
                    }
                })
            }
        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    @endpush
    @section('pageTitle', 'Kërkesë për Suport')
    <div class="container">
        <div class="site-main profile single-order">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content">
                <div class="card">
                    <div class="card-header"><h1>Kërkesë për Suport për Porosinë <a href="{{ route('profile.orders.single', $order->id) }}">#{{ $order->id }}</a></h1></div>
                    <div class="card-body">
                        <form action="{{ route('profile.orders.support.create', $order->id) }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="vendor">Zgjidhni Dyqanin për të cilin keni nevojë për suport</label>
                                        <select name="vendor" id="vendor" class="form-control">
                                            @foreach($order->ordervendor as $vendor)
                                                <option value="{{ $vendor->vendor->id }}">{!! strtoupper($vendor->vendor->name) !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="subject_choose">Arsyeja për këtë kërkesë</label>
                                        <select name="subject_choose" id="subject_choose" class="form-control">
                                            <option value="1">Porosia nuk ka mbërritur</option>
                                            <option value="2">Probleme me Produktin</option>
                                            <option value="3">Kërkesë për Rimbursim</option>
                                            <option value="9">Tjetër</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 custom-subject">
                                    <div class="form-group">
                                        <label for="subject">Arsyeja</label>
                                        <input type="text" name="subject" class="form-control" id="subject" placeholder="Subjekti" value="{{ old('subject') }}">
                                        @error('subject') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="message">Mesazhi</label>
                                        <textarea name="message" id="message" class="form-control" placeholder="Mesazhi">{{ old('message') }}</textarea>
                                        @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="image">Ngarko Foto/Dokument (Opsionale)</label>
                                        @livewire('upload-file', [
                                            'inputName' => 'attachment', 'upload' => 'multiple', 'exis' => '', 'path'=> 'ticket/', 'buttonName' => 'Ngarko Foto/Dokument', 
                                            'style'=>1, 'maxWidth'=>1000, 'maxHeight'=>1000, 'maxSize'=>6144
                                        ])
                                        @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn small c1">Dërgo</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h1 class="profile-title">Informacione për Porosinë <a href="{{ route('profile.orders.single', $order->id) }}">#{{ $order->id }}</a></h1>
                    </div>
                    <div class="card-body">
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
                                <div class="vendor-status">
                                    <p class="orders-information">Statusi për këtë porosi nga Dyqani: {!! strtoupper($vendor->vendor->name) !!} është: <mark id="order-status"><span class="statusc v{{$vendor->status}}">{{ $vOrderStatus }}</span></mark>.</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
                
            </main>
        </div>
    </div>
</x-app-layout>