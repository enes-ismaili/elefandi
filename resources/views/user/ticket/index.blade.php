<x-app-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
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
        <link  rel="stylesheet" href="{{ asset('css/user.css') }}">
    @endpush
    @section('pageTitle', 'Të gjitha Cështjet')
    <div class="container">
        <div class="site-main profile">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
                <h1 class="profile-title">Të gjitha Cështjet</h1>
                <div class="row">
                    <div class="col-12">
                        <table id="myTable" class="table">
                            <thead>
                                <tr>
                                    <th>Nr i Porosisë</th>
                                    <th>Data</th>
                                    <th>Statusi</th>
                                    <th>Dyqani</th>
                                    <th data-sortable="false" class="action-icons" width="110">Veprime</th>
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
                                    <td>{!! strtoupper($ticket->vendor->name) !!}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('profile.ticket.single', [$ticket->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
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