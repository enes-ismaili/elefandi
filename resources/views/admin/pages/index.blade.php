<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            // import {DataTable} from "simple-datatables"
            let tables = document.querySelectorAll('.table');
            tables.forEach(table => {
                const dataTable = new DataTable.DataTable(table, {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 15,
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
        <h4 class="heading">Faqet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Faqet</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{route('admin.pages.add')}}" class="btn btn-primary small tableadd c3" ><i class="fas fa-plus"></i> Shto Faqe</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5 class="table-header-t">Të gjitha Faqet</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th style="width: 75px">#</th>
                            <th>Emri i faqes</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $page)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$page->name}}</td>
                                <td class="action-icons">
                                    <a href="{{ route('pages.single', $page->slug) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    @if($page->delete && check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('admin.pages.delete', $page->id) }}" 
                                        data-text="Ju po fshini Faqen '{{ $page->name }}'"
                                        data-type="Faqe"><i class="fas fa-trash" class="action-icon"></i>
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