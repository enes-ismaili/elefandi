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
        <style>
            .dataTable-wrapper .dataTable-table tbody tr.older {
                background: #ffd0d0;
            }
        </style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Kuponat</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Kuponat</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{route('vendor.coupons.new')}}" class="btn btn-primary small tableadd c3" ><i class="fas fa-plus"></i> Shto Kupon</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5 class="table-header-t">Të gjitha Kuponat</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th style="width: 75px">#</th>
                            <th>Kuponi</th>
                            <th>Tipi i Kuponit</th>
                            <th>Kategoria</th>
                            <th>Produktet</th>
                            <th>Ulja</th>
                            <th>Fillon</th>
                            <th>Përfundon</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $now = date('Y-m-d H:i:s');
                        @endphp
                        @foreach($coupons as $coupon)
                            @php
                                if($coupon->action == 1){
                                    $ulja = '-'.$coupon->discount.'%';
                                } else {
                                    $ulja = '-'.$coupon->discount.'€';
                                }
                                if($coupon->type == 2){
                                    $couponType = 'Kupon mbi Kategoritë';
                                } elseif($coupon->type == 3){
                                    $couponType = 'Kupon mbi Produktet';
                                } else {
                                    $couponType = 'Kupon mbi Dyqanin';
                                }
                                $categories = json_decode($coupon->categories);
                                $products = json_decode($coupon->products);
                                $isCouponOlder = ($coupon->expire_date < $now) ? true : false;
                            @endphp
                            <tr class="{{ $isCouponOlder ? 'older' : '' }}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$coupon->code}}</td>
                                <td>{{ $couponType }}</td>
                                <td>
                                    @if($categories && $coupon->type == 2)
                                        @foreach($categories as $category)
                                        @php
                                            $currCategory = App\Models\Category::where('id', $category)->first();
                                        @endphp
                                        <a href="{{ route('category.single', $currCategory->slug) }}" class="table-block">{{ $currCategory->name }}</a>
                                        @endforeach
                                    @else
                                        Sështë zgjedhur
                                    @endif
                                </td>
                                <td>
                                    @if($products && $coupon->type == 3)
                                        @foreach($products as $product)
                                        @php
                                            $currProduct = App\Models\Product::where('id', $product)->first();
                                        @endphp
                                        <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}" class="table-block">{{ $currProduct->name }}</a>
                                        @endforeach
                                    @else
                                        Sështë zgjedhur
                                    @endif
                                </td>
                                <td>{{ $ulja }}</td>
                                <td>{{ Carbon\Carbon::parse($coupon->start_date)->format('d-m-Y') }}</td>
                                <td>{{ Carbon\Carbon::parse($coupon->expire_date)->format('d-m-Y') }}</td>
                                <td class="action-icons">
                                    <a href="{{ route('vendor.coupons.edit', $coupon->id) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    @if(check_permissions('delete_rights'))
                                        <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.coupons.delete', $coupon->id) }}" 
                                            data-text="Ju po fshini Kuponin '{{ $coupon->code }}'"
                                            data-type="Kupon"><i class="fas fa-trash" class="action-icon"></i>
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