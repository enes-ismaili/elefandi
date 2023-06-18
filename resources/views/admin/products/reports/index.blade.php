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
        <h4 class="heading">Raportimet e Produkteve</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.reports.index')}}">Raportimet</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Emri</th>
                                <th>Emaili</th>
                                <th>Produkti</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $report->name }}</td>
                                    <td>{{ $report->email }}</td>
                                    @if($report->product)
                                        <td><a href="{{ route('admin.products.edit', $report->product->id) }}" style="color: #007bff;">{{ $report->product->name }}</a></td>
                                    @else
                                        <td>Produkti nuk ekziston</td>
                                    @endif
                                    <td class="action-icons">
                                        <a href="{{ route('admin.products.reports.view', $report->id) }}" class="action-icon" title="Shiko"> <i class="fa fa-eye"></i></a>
                                        @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.products.reports.delete', $report->id) }}" 
                                            data-text="Ju po fshini Raportimin e '{{ $report->name }}'"
                                            data-type="Raportim"><i class="fas fa-trash" class="action-icon"></i>
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