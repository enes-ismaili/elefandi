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
        <h4 class="heading">Produktet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Produktet</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Produktet</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th style="width: 75px">#</th>
                            <th>Foto</th>
                            <th>Emri</th>
                            <th>Kategoria</th>
                            <th>Sku</th>
                            <th>Stoku</th>
                            <th>Çmimi</th>
                            <th data-sortable="false" class="action-icons" width="170">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <img src="{{ ((\File::exists('photos/products/70/'.$product->image)) ? asset('/photos/products/70/'.$product->image) : asset('/photos/products/'.$product->image) ) }}" alt="" width="50" height="50">
                            </td>
                            <td>{{$product->name}}</td>
                            <td>{{$product->category ? $product->category->name : ''}}</td>
                            <td>{{$product->sku}}</td>
                            <td>{{$product->stock}}</td>
                            <td>{{ number_format($product->price,2) }}€</td>
                            <td class="action-icons">
                                <a href="{{ route('single.product', [$product->owner->slug, $product->id]) }}" target="_blank" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                <a href="{{route('admin.products.edit', $product->id)}}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                <a href="{{route('admin.products.comments', $product->id)}}" class="action-icon" title="Vlersimet"><i class="fas fa-star" aria-hidden="true"></i></a>
                                <a href="{{route('admin.products.sales', $product->id)}}" class="action-icon" title="Fallsifiko Shitjet"><i class="fas fa-hand-holding-usd" aria-hidden="true"></i></a>
                                @if(check_permissions('delete_rights'))
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.products.delete', $product->id) }}" 
                                    data-text="Ju po fshini produktin '{!! $product->name !!}'"
                                    data-type="Produkt"><i class="fas fa-trash" class="action-icon"></i>
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