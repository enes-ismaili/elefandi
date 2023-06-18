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
                <div class="membership-invoices position-relative">
                    <h5 class="table-header-t">Faturat e papaguara</h5>
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dyqani</th>
                                <th>Emri</th>
                                <th>Data</th>
                                <th>Shitje</th>
                                <th>Komisioni</th>
                                <th>Shuma</th>
                                <th>Paguar</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoicesUnpaid as $invoice)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $invoice->vendor->name}}</td>
                                <td>{{ $invoice->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $invoice->total * 1 }}€</td>
                                <td>{{ $invoice->comission * 1 }}%</td>
                                <td>{{ $invoice->amount * 1 }}€</td>
                                <td>{{ ($invoice->paid) ? 'Po' : 'Jo' }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.vendors.membership.invoice.edit', $invoice->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="membership-invoices position-relative">
                    <h5 class="table-header-t">Faturat e paguara</h5>
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dyqani</th>
                                <th>Emri</th>
                                <th>Data</th>
                                <th>Shitje</th>
                                <th>Komisioni</th>
                                <th>Shuma</th>
                                <th>Paguar</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoicesPaid as $invoice)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $invoice->vendor->name}}</td>
                                <td>{{ $invoice->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i') }}</td>
                                <td>{{ $invoice->total * 1 }}€</td>
                                <td>{{ $invoice->comission * 1 }}%</td>
                                <td>{{ $invoice->amount * 1 }}€</td>
                                <td>{{ ($invoice->paid) ? 'Po' : 'Jo' }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.vendors.membership.invoice.edit', $invoice->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>