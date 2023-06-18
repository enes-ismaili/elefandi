<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            let tables = document.querySelectorAll('.table');
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
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Stafi</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('vendor.staff.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Staf</a>
            </div>
        </div>
        @if(count($vRequests))
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Kërkesat e çuara për shtim në menaxhimin e dyqanit</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emaili</th>
                            <th>Roli</th>
                            <th>Data e Kërkesës</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vRequests as $user)
                            <tr>
                                <td>{{ $loop->index+1 }}</td>
                                <td>{{ $user->user_email }}</td>
                                <td>{{ $user->roles->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i') }}</td>
                                <td class="action-icons">
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('vendor.staff.request.delete', [$user->id]) }}" 
                                        data-text="Ju po anulloni kërkesën për shtim stafi drejtuar '{{$user->user_email}}'"
                                        data-type="Kërkesë Stafi"><i class="fas fa-trash" class="action-icon"></i>
                                    </span>
                                    @endif
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
                <h5 class="table-header-t">Menaxhimi i Roleve</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <td>#</td>
                            <th>Emri</th>
                            <th>Mbiemri</th>
                            <th>Roli</th>
                            <th>Data e Ndryshimit</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                if($user->roles){
                                    $uRole = $user->roles()->where('type', '=', 0)->first();
                                    if($uRole){
                                        $roleId = $uRole->id;
                                        $roleName = $uRole->name;
                                    } else {
                                        $roleId = 0;
                                        $roleName = '';
                                    }
                                } else {
                                    $uRole = '';
                                    $roleId = 0;
                                    $roleName = '';
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->index+1 }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $roleName }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i') }}</td>
                                <td class="action-icons">
                                    @if($roleId != 2)
                                        <a href="{{ route('vendor.staff.edit', [$user->id, $roleId]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                        @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('vendor.staff.delete', [$user->id, $roleId]) }}" 
                                            data-text="Ju po fshini nga Stafi '{{$user->first_name.' '.$user->last_name}}'"
                                            data-type="Staf"><i class="fas fa-trash" class="action-icon"></i>
                                        </span>
                                        @endif
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