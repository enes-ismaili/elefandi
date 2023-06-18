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
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Njoftimet</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <h5>Njoftime e Pranuara që cdo dyqan mund të hedhi çdo muaj</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h6>Cdo dyqan mund të hedhi <b>{{ $limit->value }} njoftime</b> të pranuara në muaj</h6>
                    <p>Kjo nuk vlen për dyqanet që i është vënë një limit i veçantë</p>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('admin.notifications.limit.edit') }}" class="btn btn-outline-info">Ndrysho Sasinë Mujore të të gjithë dyqaneve</a>
                </div>
            </div>
        </div>
    </div>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Njoftimet në Pritje</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Dërguesi</th>
                            <th>Lloji</th>
                            <th>Titulli</th>
                            <th>Krijuar</th>
                            <th>Dërgohet</th>
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
                                } else if($notification->ntype == 4) {
                                    $notificationType = 'Ofertë Speciale';
                                } else if($notification->ntype == 5) {
                                    $notificationType = 'Ofertë Dyqani';
                                }
                            @endphp
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ ($notification->vendor_id) ? $notification->vendor->name : 'Administratori' }}</td>
                            <td>{{ $notificationType }}</td>
                            <td>{{ $notification->title }}</td>
                            <td>{{ Carbon\Carbon::parse($notification->created_at)->format('H:i d.m.Y') }}</td>
                            <td>Në Pritje Konfirmimi</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.notifications.edit', [$notification->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if(check_permissions('delete_rights'))
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.notifications.delete', [$notification->id]) }}" 
                                    data-text="Ju po fshini Njoftimin '{{ $notification->title }}'."
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
            <div class="col-12">
                <a href="{{ route('admin.notifications.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Njoftim</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Të gjitha Njoftimet</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Dërguesi</th>
                            <th>Lloji</th>
                            <th>Titulli</th>
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
                                } else if($notification->ntype == 4) {
                                    $notificationType = 'Ofertë Speciale';
                                } else if($notification->ntype == 5) {
                                    $notificationType = 'Ofertë Dyqani';
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
                            <td>{{ ($notification->vendor_id) ? $notification->vendor->name : 'Administratori' }}</td>
                            <td>{{ $notificationType }}</td>
                            <td>{{ $notification->title }}</td>
                            <td>{{ Carbon\Carbon::parse($notification->created_at)->format('H:i d.m.Y') }}</td>
                            <td>{{ $sendStatus }}</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.notifications.view', [$notification->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                @if($notification->nactive == 2)
                                    <a href="{{ route('admin.notifications.edit', [$notification->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @endif
                                {{-- <span class="deleteModal action-icon" 
                                    data-link="{{ route('admin.notifications.delete', [$notification->id]) }}" 
                                    data-text="Ju po fshini Njoftimin '{{ $notification->title }}'. <br>Nëse statusi i këtij njoftimi është Në Pritje për tu dërguar atëhere ky mesazh nuk do ti dërgohet përdoruesve."
                                    data-type="Njoftimi"><i class="fas fa-trash" class="action-icon"></i>
                                </span> --}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>