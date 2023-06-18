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
        <h4 class="heading">Përdoruesit</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.users.index')}}">Përdoruesit</a>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emri</th>
                            <th>Mbiemri</th>
                            <th>Email</th>
                            <th>Qyteti</th>
                            <th>Shteti</th>
                            <th>Regjistruar</th>
                            <th>Statusi</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                          </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{(is_numeric($user->city) && $user->country_id < 4)? ($user->cities)?$user->cities->name:$user->city : $user->city}}</td>
                                <td>{{$user->country()->name}}</td>
                                <td>{{\Carbon\Carbon::parse($user->created_at)->format('d.m.Y H:i')}}</td>
                                <td>Aktiv</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.users.single', $user->id) }}" class="action-icon" title="Shiko"> <i class="fas fa-eye"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.users.delete', $user->id) }}" 
                                        data-text="Ju po fshini përdoruesin '{{$user->first_name.' '.$user->last_name}}'"
                                        data-type="Përdorues"><i class="fas fa-trash" class="action-icon"></i>
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