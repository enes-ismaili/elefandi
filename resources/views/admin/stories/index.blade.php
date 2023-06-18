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
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.stories.index')}}">Story</a>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <h5>Story që cdo dyqan mund të hedhi çdo muaj</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h6>Cdo dyqan mund të hedhi <b>{{ $limit }} story</b> të pranuara në muaj</h6>
                    <p>Kjo nuk vlen për dyqanet që i është vënë një limit i veçantë</p>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('admin.stories.limit.edit') }}" class="btn btn-outline-info">Ndrysho Sasinë Mujore të të gjithë dyqaneve</a>
                </div>
            </div>
        </div>
    </div>
    <div class="product-area">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.stories.add') }}" class="btn btn-primary small tableadd c3"><i class="fas fa-plus"></i> Shto Story</a>
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
                            <th>Dyqani</th>
                            <th>Ndryshimi i fundit</th>
                            <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stories as $story)
                        @php
                            $statusText = 'në Pritje';
                            if($story->cactive == 1){
                                $statusText = 'Aktiv';
                            } elseif($story->cactive == 2){
                                $statusText = 'Rishikim';
                            } elseif($story->cactive == 3){
                                $statusText = 'Refuzuar';
                            }
                        @endphp
                        <tr style="background-color:{{ ($story->needaction == 1 || $story->cactive == 0) ? '#ffc10754' : '#28a74554' }}">
                            <td>{{ $loop->index + 1 }}</td>
                            <td style="min-height:70px">@if($story->items()->count())<img src="{{ asset('photos/story/'.$story->items()->where('type',1)->first()->image) }}">@endif</td>
                            <td>{{ $story->items()->where('start_story', '>', date('Y-m-1 H:i:s'))->count() }}</td>
                            <td>
                                <i>{{ ($story->needaction == 1) ? 'Story në Pritje' : 'Aktiv' }}</i>
                            </td>
                            <td>{!! ($story->vendor) ? strtoupper($story->vendor->name) : 'Nuk ekziton' !!}</td>
                            <td>{{ \Carbon\Carbon::parse($story->updated_at)->format('d-m-Y H:i') }}</td>
                            <td class="action-icons">
                                <a href="{{ route('admin.stories.edit', [$story->id]) }}" class="action-icon" title="Ndrysho"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if(check_permissions('delete_rights'))
                                <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.stories.delete', [$story->id]) }}" 
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