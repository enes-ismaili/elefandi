<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            // import {DataTable} from "simple-datatables"
            let allTables = document.querySelectorAll('.table');
            allTables.forEach(table => {
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
        <h4 class="heading">Dyqanet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.index')}}">Dyqanet</a>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        @if(count($vendorsActive))
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="table-header-t">Kërkesat në Pritje</h5>
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dyqani</th>
                                <th>Emri i Ri</th>
                                <th>Statusi</th>
                                <th>Data</th>
                                <th data-sortable="false" class="action-icons" width="150">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendorsActive as $vendor)
                                @php
                                    $nameChangeStatus = 'në Pritje';
                                    if($vendor->status == 1){
                                        $nameChangeStatus = 'Aprovuar';
                                    } elseif($vendor->status == 2){
                                        $nameChangeStatus = 'Refuzuar';
                                    }
                                @endphp
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$vendor->vendor->name}}</td>
                                    <td>{{$vendor->name}}</td>
                                    <td>{{$nameChangeStatus}}</td>
                                    <td>{{$vendor->created_at->format('d.m.Y H:i')}}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.vendor.namechange.edit', $vendor->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Kërkesat e Konfirmuara</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Dyqani</th>
                            <th>Emri i Ri</th>
                            <th>Statusi</th>
                            <th>Data</th>
                            <th data-sortable="false" class="action-icons" width="150">Veprime</th>
                          </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            @php
                                $nameChangeStatus = 'në Pritje';
                                if($vendor->status == 1){
                                    $nameChangeStatus = 'Aprovuar';
                                } elseif($vendor->status == 2){
                                    $nameChangeStatus = 'Refuzuar';
                                }
                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$vendor->vendor->name}}</td>
                                <td>{{$vendor->name}}</td>
                                <td>{{$nameChangeStatus}}</td>
                                <td>{{$vendor->created_at->format('d.m.Y H:i')}}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.vendor.namechange.edit', $vendor->id) }}" class="action-icon" title="Shiko"> <i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>