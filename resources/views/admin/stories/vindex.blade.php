<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            // import {DataTable} from "simple-datatables"
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
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
        <h4 class="heading">{{ $title }}</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.stories.index')}}">Story</a>
            </li>
        </ul>
    </x-slot>
    <div class="product-area"><div class="row">
        <div class="col-12">
            {{-- <a href="{{ route('vendor.stories.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Story</a> --}}
            <div>
                @if($stories->count() < $limit)
                    <a href="{{ route('vendor.stories.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Story</a>
                @else
                    <p class="text-warning">Ju keni arritur limitin prej <b>{{ $limit }} Story</b> (Story në pritje dhe Story të aprovuara)<br> Fshini Story ekzistues për të shtuar të reja ose kontaktoni me administratoret për të shuar limitin <br> Kjo nuk ju limiton për Foto/Video brenda Story ekzistues</p>
                @endif
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-lg-12">
                <h5 class="table-header-t">Story-t e mia</h5>
                <table id="myTable" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto Kryesore</th>
                            <th>Story</th>
                            <th>Statusi</th>
                            <th>Data e Krijimit</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stories as $story)
                        @php
                            $statusText = 'në Pritje';
                            if($story->cactive == 1){
                                $statusText = 'Aprovuar';
                            } elseif($story->cactive == 2){
                                $statusText = 'Rishikim';
                            } elseif($story->cactive == 3){
                                $statusText = 'Refuzuar';
                            }
                        @endphp
                        <tr style="background-color: @if($story->cactive == 1 && $story->needaction == 0) #28a74554 @elseif($story->cactive == 2) #17a2b854 @elseif($story->cactive == 3) #dc354554 @else #ffc10754 @endif">
                            <td>{{ $loop->index + 1 }}</td>
                            <td style="min-height:70px">@if($story->items()->count())<img src="{{ asset('photos/story/'.$story->items()->where('type',1)->first()->image) }}">@endif</td>
                            <td>{{ $story->items()->count() }}</td>
                            <td>
                                {{ $statusText }}<br>
                                <i>{{ ($story->needaction == 1) ? 'Story në Pritje' : '' }}</i>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($story->created_at)->format('d-m-Y H:i') }}</td>
                            <td class="action-icons">
                                <a href="{{ route('vendor.stories.edit', [$story->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if(check_permissions('delete_rights'))
                                    <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                        data-link="{{ route('vendor.stories.delete', [$story->id]) }}" 
                                        data-text="Ju po fshini Story-in '{{$story->name}}'"
                                        data-type="Story"><i class="fas fa-trash" class="action-icon"></i>
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