<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            let dataTables = document.querySelectorAll('table.table');
            dataTables.forEach(dataTable => {
                new DataTable.DataTable(dataTable, {
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
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Njoftimet</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                @if($notificationsC <= $limit)
                    <a href="{{ route('vendor.notifications.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Njoftim</a>
                @else
                    <p class="text-warning">Ju keni arritur limitin mujor prej <b>{{ $limit }} Njoftimesh</b> mujore (Njoftime në pritje dhe Njoftime të aprovuara)</p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Njoftimet në Pritje</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titulli</th>
                            <th>Lloji</th>
                            <th>Krijuar</th>
                            <th>Dërgohet</th>
                            <th>Statusi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notificationsNA as $notification)
                            @php
                                if($notification->ntype == 1){
                                    $notificationType = 'Dyqan';
                                } else if($notification->ntype == 2) {
                                    $notificationType = 'Produkt';
                                } else if($notification->ntype == 3) {
                                    $notificationType = 'Kupon';
                                }
                                $sendAt = Carbon\Carbon::parse($notification->send_at);
                                $isSended = Carbon\Carbon::now()->lt($sendAt);
                                if($notification->nactive == 2) {
                                    $sendStatus = 'REFUZUAR';
                                } else {
                                    if($isSended){
                                        $sendStatus = $sendAt;
                                    } else {
                                        $sendStatus = 'DËRGUAR';
                                    }
                                }
                            @endphp
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $notification->title }}</td>
                            <td>{{ $notificationType }}</td>
                            <td>{{ Carbon\Carbon::parse($notification->created_at)->format('H:i d.m.Y') }}</td>
                            <td>{{ $sendStatus }}</td>
                            <td>Në Pritje Konfirmimi</td>
                            <td class="action-icons">
                                <a href="{{ route('vendor.notifications.view', [$notification->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                @if(check_permissions('delete_rights'))
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('vendor.notifications.delete', [$notification->id]) }}" 
                                    data-text="Ju po fshini Njoftimin '{{ $notification->title }}'"
                                    data-type="Njoftimi"><i class="fas fa-trash" class="action-icon"></i>
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
            <div class="col-lg-12">
                <h5 class="table-header-t">Njoftimet e Dërguara</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titulli</th>
                            <th>Lloji</th>
                            <th>Krijuar</th>
                            <th>Dërgohet</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            @php
                                if($notification->ntype == 1){
                                    $notificationType = 'Dyqan';
                                } else if($notification->ntype == 2) {
                                    $notificationType = 'Produkt';
                                } else if($notification->ntype == 3) {
                                    $notificationType = 'Kupon';
                                }
                                $sendAt = Carbon\Carbon::parse($notification->send_at);
                                $isSended = Carbon\Carbon::now()->lt($sendAt);
                                if($notification->nactive == 2) {
                                    $sendStatus = 'REFUZUAR';
                                } else {
                                    if($isSended){
                                        $sendStatus = $sendAt;
                                    } else {
                                        $sendStatus = 'DËRGUAR';
                                    }
                                }
                            @endphp
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $notification->title }}</td>
                            <td>{{ $notificationType }}</td>
                            <td>{{ Carbon\Carbon::parse($notification->created_at)->format('H:i d.m.Y') }}</td>
                            <td>{{ $sendStatus }}</td>
                            <td class="action-icons">
                                <a href="{{ route('vendor.notifications.view', [$notification->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>