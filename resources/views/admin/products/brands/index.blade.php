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
        <h4 class="heading">Brandet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.brands.index')}}">Brandet</a>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ open: false }">
        <a href="{{ route('admin.products.brands.add') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Shto Brand</a>
        <div class="product-area mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Emri</th>
                                <th>Ikona/Foto</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>{{ ($brand->icon) ? $brand->icon : $brand->image }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.products.brands.edit', $brand->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
                                        @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.products.brands.delete', $brand->id) }}" 
                                            data-text="Ju po fshini Brand-in '{{ $brand->name }}'"
                                            data-type="Brand"><i class="fas fa-trash" class="action-icon"></i>
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