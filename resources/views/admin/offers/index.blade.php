<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            // import {DataTable} from "simple-datatables"
            let tables = document.querySelectorAll('#myTable');
            tables.forEach(table => {
                const dataTable = new DataTable.DataTable(table, {
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
            })
        </script>
    @endpush
    @push('styles')
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ofertat</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Ofertat</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{route('admin.offers.new')}}" class="btn btn-primary small tableadd c3" ><i class="fas fa-plus"></i> Shto Ofertë</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12" style="margin: 25px 0;border-bottom: 1px solid #ddd;">
                <h5 class="table-header-t">Ofertat Speciale</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th style="width: 75px">#</th>
                            <th>Emri</th>
                            <th>Produkte</th>
                            <th>Nisja</th>
                            <th>Përfundimi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($specialOffers as $offer)
                            @php
                                // if($coupon->action == 1){
                                //     $ulja = '-'.$coupon->discount.'%';
                                // } else {
                                //     $ulja = '-'.$coupon->discount.'€';
                                // }
                                // $vendors = json_decode($coupon->vendors);
                                // $categories = json_decode($coupon->categories);
                                // $products = json_decode($coupon->products);
                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$offer->name}}</td>
                                <td>{{ count($offer->details) }}</td>
                                <td>{{\Carbon\Carbon::parse($offer->start_date)->format('d.m.Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($offer->expire_date)->format('d.m.Y')}}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.offers.edit', $offer->id) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.offers.delete', $offer->id) }}" 
                                        data-text="Ju po fshini Ofertën '{{ $offer->name }}'"
                                        data-type="Ofertë Speciale"><i class="fas fa-trash" class="action-icon"></i>
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <h5 class="table-header-t">Ofertat e Dyqaneve</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th style="width: 75px">#</th>
                            <th>Dyqani</th>
                            <th>Emri</th>
                            <th>Tipi</th>
                            <th>Ulja</th>
                            <th>Nisja</th>
                            <th>Përfundimi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                            @php
                                // if($coupon->action == 1){
                                //     $ulja = '-'.$coupon->discount.'%';
                                // } else {
                                //     $ulja = '-'.$coupon->discount.'€';
                                // }
                                // $vendors = json_decode($coupon->vendors);
                                // $categories = json_decode($coupon->categories);
                                // $products = json_decode($coupon->products);
                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{!! ($offer->vendor) ? strtoupper($offer->vendor->name) : 'Nuk ekziston' !!}</td>
                                <td>{{$offer->name}}</td>
                                <td>
                                    @if($offer->type == 1)
                                        Oferë për Dyqanin
                                    @elseif($offer->type == 2)
                                        Oferë për Kategorinë
                                    @elseif($offer->type == 3)
                                        Oferë për disa Produktet
                                    @endif
                                </td>
                                <td>
                                    @if($offer->type == 3)
                                        Ulje për secilin
                                    @else
                                        @if($offer->action == 1)
                                            -{{$offer->discount}}%
                                        @elseif($offer->action == 2)
                                            -{{$offer->discount}}€
                                        @endif
                                    @endif
                                </td>
                                <td>{{\Carbon\Carbon::parse($offer->start_date)->format('d.m.Y')}}</td>
                                <td>{{\Carbon\Carbon::parse($offer->expire_date)->format('d.m.Y')}}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.offers.edit', $offer->id) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.offers.delete', $offer->id) }}" 
                                        data-text="Ju po fshini Ofertën '{!! $offer->name."'' nga '".(($offer->vendor)?strtoupper($offer->vendor->name):'Nuk ekziston') !!}'"
                                        data-type="Ofertë Dyqani"><i class="fas fa-trash" class="action-icon"></i>
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>