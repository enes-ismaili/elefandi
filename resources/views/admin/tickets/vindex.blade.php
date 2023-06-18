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
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Kërkesa për Suport</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Kërkesa për Suport</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>Nr i Porosisë</th>
                            <th>Data</th>
                            <th>Statusi</th>
                            <th>Përdoruesi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        @php
                            $ticketStatus = 'Në Pritje';
                            if($ticket->status == 1){
                                $ticketStatus = 'Përgjigjur';
                            } else if($ticket->status == 2){
                                $ticketStatus = 'Anulluar';
                            } else if($ticket->status == 3){
                                $ticketStatus = 'Mbyllur';
                            } else if($ticket->status == 4){
                                $ticketStatus = 'Rishikim nga Elefandi';
                            } else if($ticket->status == 6){
                                $ticketStatus = 'Mbyllur Përfundimisht';
                            } else if($ticket->status == 7){
                                $ticketStatus = 'Rikthim Pagese';
                            }
                        @endphp
                        <tr>
                            <td>{{ $ticket->order_id }}</td>
                            <td>{{ \Carbon\Carbon::parse($ticket->created_at)->format('d-m-Y H:i') }}</td>
                            <td class="statusc v{{$ticket->status}}">{{ $ticketStatus }}</td>
                            <td>{{ $ticket->user->first_name.' '.$ticket->user->last_name }}</td>
                            <td class="action-icons">
                                <a href="{{ route('vendor.ticket.single', [$ticket->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>