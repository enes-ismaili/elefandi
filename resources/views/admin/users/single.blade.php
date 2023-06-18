<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.table-responsive.user-base th {
    width: 125px;
    text-align: left;
}
</style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Përdoruesit</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.users.index')}}">Përdoruesit</a>
            </li>
        </ul>
    </x-slot>
    <div class="card mt-3">
        <div class="card-header">
            <h5>Të dhënat e Përdoruesit</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive show-table user-base">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>#ID :</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Emri :</th>
                                    <td>{{ $user->first_name.' '.$user->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email :</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Telefon :</th>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Krijuar më :</th>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d.m.Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive show-table user-base">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Adresa :</th>
                                    <td>{{ $user->address }}</td>
                                </tr>
                                <tr>
                                    <th>Qyteti :</th>
                                    <td>{{ ((is_numeric($user->city) && $user->country_id < 4) ? $user->cities->name : $user->city) }}</td>
                                </tr>
                                <tr>
                                    <th>Kodi Postal :</th>
                                    <td>{{ $user->zipcode }}</td>
                                </tr>
                                <tr>
                                    <th>Shteti :</th>
                                    <td>{{ $user->country()->name }}</td>
                                </tr>
                                <tr>
                                    <th>Statusi :</th>
                                    <td>{{ ($user->status == 1) ? 'Aktiv' : 'Bllokuar' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            <h5>Adresat e Përdoruesit</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Emri</th>
                            <th>Telefon</th>
                            <th>Adresa</th>
                            <th>Qyteti</th>
                            <th>Shteti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->addresses as $address)
                            <tr>
                                <td>{{ $address->name }}</td>
                                <td>{{ $address->phone }}</td>
                                <td>{{ $address->address.', '.$address->zipcode }}<br>{{ $address->address2 }}</td>
                                <td>{{ ((is_numeric($address->city) && $address->country_id < 4) ? $address->cityF->name : $address->city) }}</td>
                                <td>{{ $address->country->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">
            <h5>Prositë e kryera nga ky Përdorues</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nr.</th>
                            <th>Data</th>
                            <th>Statusi</th>
                            <th>Pagesa</th>
                            <th>Dyqani</th>
                            <th>Shuma</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->orders as $order)
                            @php
                                $orderStatus = 'Duke Procesuar';
                                if($order->status == 1){
                                    $orderStatus = 'Dërguar';
                                } else if($order->status == 3){
                                    $orderStatus = 'Anulluar';
                                }
                                $orderPayment = 'Para në dorë';
                            @endphp
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</td>
                                <td class="statusc v{{$order->status}}">{{ $orderStatus }}</td>
                                <td>{{ $orderPayment }}</td>
                                <td>
                                    @foreach($order->ordervendor as $vendor)
                                        {{ ($vendor->vendor)?$vendor->vendor->name:'Nuk ekziston' }}@if(!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>{{ ($order->value+$order->transport)*1 }}€</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.orders.single', [$order->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('admin.orders.track', [$order->id]) }}" class="action-icon" title="Gjurmo"><i class="fas fa-truck"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>