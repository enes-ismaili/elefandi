<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Slideri Kryesor Mobile</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Slideri Kryesor Mobile</span>
            </li>
        </ul>
    </x-slot>
    <form action="{{route('admin.homesettings.slidermobile.update')}}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Slideri Kryesor</h5>
                <a class="btn btn-primary add-button tright" href="{{route('admin.homesettings.slidermobile.add')}}">Shto Slider</a>
            </div>
            <div class="card-body">
                @if($sliders->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Titulli</th>
                                <th>Renditja</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sliders as $slider)
                                <tr>
                                    <td><span class="img-prev">{!! ($slider->image) ? '<img src="'.asset('photos/slider/'.$slider->image).'" alt="">' : '' !!}</span></td>
                                    <td>{{ $slider->olink }}</td>
                                    <td>{{ $slider->corder }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.homesettings.slidermobile.edit', [$slider->id]) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.homesettings.slidermobile.delete', [$slider->id]) }}" 
                                            data-text="Ju po fshini Slider-in '{{$slider->olink}}'"
                                            data-type="Slider"><i class="fas fa-trash" class="action-icon"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </form>
</x-admin-layout>