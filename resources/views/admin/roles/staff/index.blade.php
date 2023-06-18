<x-admin-layout>
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
    @endpush
    <x-slot name="breadcrumb">
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
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
                <a href="{{ route('admin.staff.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Staf</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Menaxhimi i Roleve</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>Emri</th>
                            <th>Mbiemri</th>
                            <th>Roli</th>
                            <th>Data e Ndryshimit</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            {{-- @ray($role) --}}
                            @if(count($role->users))
                            @foreach($role->users as $user)
                            <tr>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i') }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('admin.staff.edit', [$user->id, $role->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.staff.delete', [$user->id, $role->id]) }}" 
                                        data-text="Ju po fshini nga Stafi '{{ $user->first_name.' '.$user->last_name }}'"
                                        data-type="Staf"><i class="fas fa-trash" class="action-icon"></i>
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>