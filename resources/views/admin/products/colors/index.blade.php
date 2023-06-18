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
        <h4 class="heading">Ngjyrat</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.colors.index')}}">Ngjyrat</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <a href="{{ route('admin.products.colors.add') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Shto Ngjyrë</a>
            <div class="row">
                <div class="col-lg-12">
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Emri</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach($colors as $color)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $color->name }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.products.colors.edit', $color->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                        @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.products.colors.delete', $color->id) }}" 
                                            data-text="Ju po fshini Ngjyrën e '{{ $color->name }}'"
                                            data-type="Ngjyrë"><i class="fas fa-trash" class="action-icon"></i>
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
    </div>
</x-admin-layout>