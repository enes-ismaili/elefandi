<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.img-prev {
    width: 40px;
    height: 40px;
    font-size: 30px;
    position: relative;
    display: inline-block;
}
.img-prev img {
    width: 35px;
    height: 35px;
    position: absolute;
    top: 0;
    left: 0;
    object-fit: cover;
}
</style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Vecoritë</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Vecoritë</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <h5>Vecoritë e Faqes</h5>
            @if(count($features) < 4)
                <a class=" btn btn-primary" href="{{route('admin.homesettings.addFeaturedProduct')}}"add-button tright">Shto</a>
            @endif
        </div>
        <div class="card-body">
            @if(count($features))
                @if (Session::has('error'))
                    <span class="text-danger error">{{ Session::get('error') }}</span>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titulli</th>
                                <th>Përshkrimi</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($features as $feature)
                                <tr>
                                    <td><span class="img-prev">{!! ($feature->icon) ? '<i class="'.$feature->icon.'"></i>' : '<img src="'.asset('photos/images/'.$feature->image).'" alt="">' !!}</span></td>
                                    <td>{{ $feature->name }}</td>
                                    <td>{{ $feature->description }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.homesettings.editfeatures', [$feature->id]) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.homesettings.deletefeatures', [$feature->id]) }}" 
                                            data-text="Ju po fshini Vecorinë '{{$feature->name}}'"
                                            data-type="Vecori"><i class="fas fa-trash" class="action-icon"></i>
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
</x-admin-layout>