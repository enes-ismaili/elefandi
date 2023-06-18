<x-app-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script type="module">
            // import {DataTable} from "simple-datatables"
            // import {DataTable} from "../js/datatables.js"
            // const dataTable = new simpleDatatables.DataTable("#myTable", {
            //     searchable: false,
            //     fixedHeight: true,
            // })
        </script>
        <script>
            // import {DataTable} from "simple-datatables"
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 15,
                fixedColumns: false,
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
    <link rel="stylesheet" href="{{ mix('css/user.css') }}">
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush
    @section('pageTitle', 'Të gjitha Porositë')
    <div class="container">
        <div class="site-main profile">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                <h1 class="profile-title">Të gjitha Porositë</h1>
                <div class="row">
                    <div class="col-12">
                        <table id="myTable" class="table">
                            <thead>
                                <tr>
                                    <th style="max-width:80px">Nr i Porosisë</th>
                                    <th>Data</th>
                                    <th>Statusi</th>
                                    <th>Metoda e Pagesës</th>
                                    <th>Shuma</th>
                                    <th data-sortable="false" class="action-icons" width="110">Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
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
                                <tr>
                                    <td style="max-width:80px">{{ $order->id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i') }}</td>
                                    <td class="statusc v{{$order->status}}">{{ $orderStatus }}</td>
                                    <td>{{ $orderPayment }}</td>
                                    <td>{{ number_format(($order->value+$order->transport), 2) }}€</td>
                                    <td class="action-icons">
                                        <a href="{{ route('profile.orders.single', [$order->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        <a href="{{ route('profile.orders.track', [$order->id]) }}" class="action-icon" title="Gjurmo"><i class="fas fa-truck"></i></a>
                                        <a href="{{ route('profile.orders.support', $order->id) }}" class="action-icon" title="Ngri Çështje"><i class="fas fa-envelope"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>