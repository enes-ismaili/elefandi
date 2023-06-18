<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            let dataTables = document.querySelectorAll('table.table');
            dataTables.forEach(dataTable => {
                new DataTable.DataTable(dataTable, {
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
                <span>Reklamat në {{ $mads->name }}</span>
            </li>
        </ul>
    </x-slot>
    <div class="product-area">
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Reklamat në Pritje</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto Dektop</th>
                            <th>Dyqani</th>
                            <th>Krijuar</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adsW as $ad)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{!! ($ad->dimage) ? '<img src="'.asset('photos/ads/'.$ad->dimage).'" height="50">' : '' !!}</td>
                            <td>{{ ($ad->vendor_id) ? $ad->vendor->name : 'Administratori' }}</td>
                            <td>{{ $ad->view }}</td>
                            <td>{{ Carbon\Carbon::parse($ad->created_at)->format('H:i d.m.Y') }}</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.ads.single.edit', [$mads->id, $ad->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.ads.single.delete', [$mads->id, $ad->id]) }}" 
                                    data-text="Ju po fshini Reklamën '{{ ($ad->vendor_id) ? 'e Dyqanit '.$ad->vendor->name : '' }}'."
                                    data-type="Reklamë"><i class="fas fa-trash" class="action-icon"></i>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.ads.single.add', $mads->id) }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Reklamë</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Të gjitha Reklamat</h5>
                <table id="myTable" class="table" style="margin: 25px 0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto Dektop</th>
                            <th>Dyqani</th>
                            <th>Shikime</th>
                            <th>Klikime</th>
                            <th>Krijuar</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ads as $ad)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{!! ($ad->dimage) ? '<img src="'.asset('photos/ads/'.$ad->dimage).'" height="50">' : '' !!}</td>
                            <td>{{ ($ad->vendor_id) ? $ad->vendor->name : 'Administratori' }}</td>
                            @if($ad->fvaction == 0)
                                <td>{{ $ad->view }}</td>
                            @elseif($ad->fvaction == 1)
                                <td>{{ $ad->view + $ad->fview }} (Real: {{ $ad->view }})</td>
                            @elseif($ad->fvaction == 2)
                                <td>{{ ($ad->view && $ad->fview) ? ($ad->view * $ad->fview) : $ad->view }} (Real: {{ $ad->view }})</td>
                            @endif
                            @if($ad->fcaction == 0)
                                <td>{{ $ad->click }}</td>
                            @elseif($ad->fcaction == 1)
                                <td>{{ $ad->click + $ad->fclick }} (Real: {{ $ad->click }})</td>
                            @elseif($ad->fcaction == 2)
                                <td>{{ ($ad->click && $ad->fclick) ? ($ad->click * $ad->fclick) : $ad->click }} (Real: {{ $ad->click }})</td>
                            @endif
                            <td>{{ Carbon\Carbon::parse($ad->created_at)->format('H:i d.m.Y') }}</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.ads.single.edit', [$mads->id, $ad->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.ads.single.delete', [$mads->id, $ad->id]) }}" 
                                    data-text="Ju po fshini Reklamën e krijuar nga '{{ ($ad->vendor_id) ? 'Dyqani '.$ad->vendor->name : 'Administratori' }}'."
                                    data-type="Reklamë"><i class="fas fa-trash" class="action-icon"></i>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>