<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 15,
                columns: [
                    { select: [2], sortable: false },
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
        <h4 class="heading">Emailet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.emails.index')}}">Emailet</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            {{-- <a href="{{ route('admin.emails.send') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Dërgo Email</a> --}}
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
                            @foreach($emails as $email)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $email->name }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.emails.edit', $email->id) }}" class="action-icon" title="Ndrysho"> <i class="fas fa-edit"></i></a>
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