<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            let tables = document.querySelectorAll('.table');
            // import {DataTable} from "simple-datatables"
            tables.forEach(stable => {
                const dataTable = new DataTable.DataTable(stable, {
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
        <style>
            .dataTable-wrapper .dataTable-table tbody tr.active {
                background-color: rgb(173, 244, 139);
            }
            .dataTable-wrapper .dataTable-table tbody tr.expire {
                background-color: rgb(242, 133, 139);
            }
        </style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Membership</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.index')}}">Membership</a>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{route('admin.vendors.membership.add', $vid)}}" class="btn btn-primary small tableadd c3" ><i class="fas fa-plus"></i> Shto Membership</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                @php
                    $todayDate = time();
                @endphp
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emri</th>
                            <th>Lloji</th>
                            <th>Shuma</th>
                            <th>Data e Fillimit</th>
                            <th>Data e Mbarimit</th>
                            <th>Përshkrimi</th>
                            <th>Statusi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                          </tr>
                    </thead>
                    <tbody>
                        @foreach($memberships as $membership)
                            @php
                                $membershipType = 'Fikse';
                                $membershipTypeS = '€';
                                if($membership->type == 2){
                                    $membershipType = 'Përqindje';
                                    $membershipTypeS = '%';
                                }
                            @endphp
                            <tr class="@if($membership->active && (strtotime($membership->start_date) < $todayDate) && (strtotime($membership->end_date) > $todayDate)) active @elseif($membership->active && (strtotime($membership->end_date) < $todayDate)) expire @endif">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$membership->vendor->name}}</td>
                                <td>{{$membershipType}}</td>
                                <td>{{($membership->amount * 1).$membershipTypeS.'/m'}}</td>
                                <td>{{\Carbon\Carbon::parse($membership->start_date)->format('d.m.Y H:i')}}</td>
                                @if($membership->type == 2)
                                    <td>Pa Limit</td>
                                @else
                                    <td>{{\Carbon\Carbon::parse($membership->end_date)->format('d.m.Y H:i')}}</td>
                                @endif
                                <td>{{$membership->description}}</td>
                                <td>{{($membership->active)?'Aktiv':'Jo Aktiv'}}</td>
                                <td class="action-icons">
                                    {{-- <a href="{{ route('single.vendor', $membership->id) }}"> <i class="fas fa-eye"></i> Shiko</a> --}}
                                    <a href="{{ route('admin.vendors.membership.edit', [$vid, $membership->id]) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.vendors.membership.delete', [$vid, $membership->id]) }}" 
                                        data-text="Ju po fshini nje prej membership të dyqanit '{!! $membership->vendor->name !!}'"
                                        data-type="Dyqan"><i class="fas fa-trash" class="action-icon"></i>
                                    </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @if($membershipsInvoice->count())
                    <div class="membership-invoices position-relative">
                        <h5 class="table-header-t">Të gjitha Faturat</h5>
                        <table id="myTable" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Data</th>
                                    <th>Shitje</th>
                                    <th>Komisioni</th>
                                    <th>Shuma</th>
                                    <th>Paguar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($membershipsInvoice as $invoice)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i') }}</td>
                                    <td>{{ $invoice->total * 1 }}€</td>
                                    <td>{{ $invoice->comission * 1 }}%</td>
                                    <td>{{ $invoice->amount * 1 }}€</td>
                                    <td>{{ ($invoice->paid) ? 'Po' : 'Jo' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>