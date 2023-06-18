<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 15,
                columns: [
                    { select: [4], sortable: false },
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
        <h4 class="heading">Vlersimet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.variants.index')}}">Vlersimet</a>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ open: false }">
        <div class="product-area mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <table id="myTable" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Emri</th>
                                <th>Vlersimi</th>
                                <th>Komenti</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach($ratings as $rating)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $rating->user->first_name.' '.$rating->user->last_name }}</td>
                                    <td>{{ $rating->rating }}</td>
                                    <td>{{ $rating->comment }}</td>
                                    <td class="action-icons">
                                        @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi"
                                            data-link="{{ route('admin.products.comments.delete', [$product->id, $rating->id]) }}" 
                                            data-text="Ju po fshini Vlersimin e '{{ $rating->user->first_name.' '.$rating->user->last_name }}'"
                                            data-type="Vlersim"><i class="fas fa-trash" class="action-icon"></i>
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