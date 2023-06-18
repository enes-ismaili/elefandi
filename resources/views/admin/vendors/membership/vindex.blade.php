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
        <h4 class="heading">Menaxho Antarësinë</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.membership.index')}}">Menaxho Antarësinë</a>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-body">
            <div class="membership-active">
                @php
                @endphp
                @if($vendor->amembership->count())
                    @php
                        $activeMembership = $vendor->amembership->first();
                    @endphp
                    @if($activeMembership->type == 2)
                        <div class="" style="border:1px solid #eee;">
                            <div class="card-header">Pako Mujore</div>
                            <div class="card-body">
                                <ul>
                                    <li>Shfaqja e shënjës si biznes i verifikur</li>
                                    <li>Fatura në fund çdo fund muaji për komisionin tuaj</li>
                                </ul>
                                <h4>Komisioni juaj mujor është {{ $activeMembership->amount * 1 }}%</h4>
                            </div>
                        </div>
                    @else
                        <div class="" style="border:1px solid #eee;">
                            <div class="card-header">Pako Mujore</div>
                            <div class="card-body">
                                <ul>
                                    <li>Shfaqja e shënjës si biznes i verifikur</li>
                                    <li>Shitje pa limit dhe pa provizion</li>
                                </ul>
                                <h4>Ju keni 2 muaj falas dhe pastaj nga <del>19.95€</del> do të paguani vetëm {{ $activeMembership->amount * 1 }}€ në muaj për 12 muajt në vazhdim.</h4>
                                <p>Për shkak të pandemisë Covid-19, ju do të përfitoni 2 muaj falas dhe si dyqan i ri në Elefandi, po ashtu do të keni zbritje të pakos për 12 muajt e ardhshëm nga <del>19.95€</del> ne 9.95€ në muaj</p>
                            </div>
                        </div>
                    @endif
                @else
                        <p>Ju keni Antarësim Aktiv</p>
                @endif
            </div>
            @if(current_vendor()->invoices->count())
            <div class="divider mt-5 mb-4"></div>
            <div class="membership-invoices position-relative">
                <h5 class="table-header-t">Të gjitha Faturat</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emri</th>
                            <th>Data</th>
                            <th>Shitje</th>
                            <th>Komisioni</th>
                            <th>Shuma</th>
                            <th>Paguar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendor->invoices()->orderBy('created_at', 'DESC')->get() as $invoice)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $invoice->name }}</td>
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
            @if(current_vendor()->membership->count())
            <div class="divider mt-5 mb-4"></div>
            <div class="old-membership mt-5">
                <h5>Të gjitha abonimet Tuaja</h5>
                @foreach ($vendor->membership as $membership)
                    <div class="mt-2" style="border:1px solid #eee;">
                        <div class="card-header">
                            <div class="left">
                                <span>Nga Data: {{ \Carbon\Carbon::parse($membership->start_date)->format('d.m.Y') }}</span>
                                <span style="margin-left: 15px;">Deri më: {{ \Carbon\Carbon::parse($membership->end_date)->format('d.m.Y') }}</span>
                            </div>
                            <div class="right">
                                @if($membership->type == 1)
                                    <span>Pagësë mujore : {{ $membership->amount * 1 }}€</span>
                                @elseif($membership->type == 2)
                                    <span>Komision mujor : {{ $membership->amount * 1 }}%</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">{{ $membership->description }}</div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>