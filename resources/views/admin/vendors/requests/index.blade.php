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
        <div class="row">
            <div class="col-lg-12">
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emri</th>
                            <th>Email</th>
                            <th>Qyteti</th>
                            <th>Shteti</th>
                            <th>Pronari</th>
                            <th>Regjistruar</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                          </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$vendor->name}}</td>
                                <td>{{$vendor->email}}</td>
                                <td>{{(is_numeric($vendor->city) && $vendor->country_id < 4)?$vendor->cities->name : $vendor->city}}</td>
                                <td>{{$vendor->country->name}}</td>
                                <td>{{$vendor->owners()->exists() ? $vendor->owners()->first()->fullName() : ''}}</td>
                                <td>{{$vendor->created_at->format('d.m.Y H:i')}}</td>
                                <td class="action-icons">
                                    {{-- <a href="{{ route('single.vendor', $vendor->slug) }}"> <i class="fas fa-eye"></i> Shiko</a> --}}
                                    <a href="{{ route('admin.vendors.requests.edit', $vendor->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.vendors.requests.delete', $vendor->id) }}" 
                                        data-text="Ju po fshini Kërkesën nga '{{$vendor->name}}'"
                                        data-type="Kërkesë Regjistrimi"><i class="fas fa-trash" class="action-icon"></i>
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