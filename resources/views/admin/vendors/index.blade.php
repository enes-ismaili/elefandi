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
                            {{-- <th>Qyteti</th> --}}
                            <th>Shteti</th>
                            <th>Pronari</th>
                            <th>Regjistruar</th>
                            <th>Statusi</th>
                            <th>Membership Expire</th>
                            <th data-sortable="false" class="action-icons" width="170">Veprime</th>
                          </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            @php
                                $currentStatus = 'Jo Aktiv';
                                if($vendor->vstatus == 1){
                                    $currentStatus = 'Aktiv';
                                } elseif($vendor->vstatus == 2){
                                    $currentStatus = 'Pa Paguar';
                                }
                            @endphp
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{!! $vendor->name !!}{!! ($vendor->verified) ? '<img src="'.asset('images/verified.png').'" width="14" style="margin-top: -2px;margin-left: 5px;">' : '' !!}</td>
                                <td>{{$vendor->email}}</td>
                                {{-- <td>{{$vendor->city}}</td> --}}
                                <td>{{$vendor->country()->name}}</td>
                                <td>{{$vendor->owners()->exists() ? $vendor->owners()->first()->fullName() : ''}}</td>
                                <td>{{$vendor->created_at->format('d.m.Y H:i')}}</td>
                                <td>{{ $currentStatus }}</td>
                                <td>{{ ($vendor->amembership->count()) ? $vendor->amembership->first()->end_date : 'Ska Membership Aktiv' }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('single.vendor', $vendor->slug) }}" class="action-icon" target="_blank" title="Shiko"> <i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                    <a href="{{ route('admin.vendors.membership.index', $vendor->id) }}" class="action-icon" title="Membership"><i class="far fa-money-bill-alt"></i></a>
                                    <a href="{{ route('admin.vendors.login', $vendor->id) }}" class="action-icon" title="Hyr si ky dyqan"><i class="fas fa-sign-in-alt"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.vendor.delete', $vendor->id) }}" 
                                        data-text="Ju po fshini dyqanin '{!! $vendor->name !!}'"
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
    </div>
</x-admin-layout>