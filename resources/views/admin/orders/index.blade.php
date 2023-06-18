<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            // import {DataTable} from "simple-datatables"
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 15,
                columns: [
                    { select: [2,3], sortable: false },
                ],
                labels: {
                    placeholder: "Kërko...",
                    perPage: "{select} produkte për faqe",
                    noRows: "Nuk u gjet asnjë rezultat",
                    info: "Po shihni {start} deri në {end} të {rows} rezultateve",
                },
                layout: {
                    top: "{search}",
                    bottom: "{select}{pager}"
                },
            })
        </script>
    @endpush
    @push('styles')
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">{{ $title }}</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.orders.index')}}">Porositë</a>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">{{ $title }}</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>Nr i Porosisë</th>
                            <th>Data</th>
                            <th>Statusi</th>
                            {{-- <th>Metoda e Pagesës</th> --}}
                            <th>Dyqanet</th>
                            <th>Shuma</th>
                            <th data-sortable="false" class="action-icons" width="120">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
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
                            {{-- <td>{{ $orderPayment }}</td> --}}
                            <td>
                                @foreach($order->ordervendor as $vendor)
                                    {!! ($vendor->vendor)? strtoupper($vendor->vendor->name) : 'Nuk ekziston' !!}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>{{ number_format(($order->value+$order->transport),2) }}€</td>
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