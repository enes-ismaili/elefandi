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
                <span>Rolet</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.roles.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Rol</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Menaxhimi i Roleve</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emri</th>
                            <th>Rol për</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ ($role->type == 1) ? 'Administratorët' : 'Dyqanet' }}</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.roles.edit', [$role->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                @if($role->can_edit && check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.roles.delete', [$role->id]) }}" 
                                        data-text="Ju po fshini Rolin '{{ $role->name }}'"
                                        data-type="Rol"><i class="fas fa-trash" class="action-icon"></i>
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