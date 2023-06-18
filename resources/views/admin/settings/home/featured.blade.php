<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Produktet e Preferuara</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Produktet e Preferuara</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header">
            <h5>Produktet e Përzgjedhur</h5>
            @if(count($featured) < 3)
                <a class=" btn btn-primary" href="{{route('admin.homesettings.addFeaturedProduct')}}"add-button tright">Shto</a>
            @endif
        </div>
        <div class="card-body">
            @if(count($featured))
                @if (Session::has('error'))
                    <span class="text-danger error">{{ Session::get('error') }}</span>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Titulli</th>
                                <th>Linku</th>
                                <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($featured as $feature)
                                <tr>
                                    <td><img src="{{asset('/photos/images/'.$feature->image)}}" alt=""></td>
                                    <td>{{ $feature->name }}</td>
                                    <td>{{ $feature->link }}</td>
                                    <td class="action-icons">
                                        <a href="{{ route('admin.homesettings.editFeaturedProduct', [$feature->id]) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                        <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                            data-link="{{ route('admin.homesettings.deleteFeaturedProduct', [$feature->id]) }}" 
                                            data-text="Ju po fshini Produktin '{{$feature->name}}'"
                                            data-type="Produkt"><i class="fas fa-trash" class="action-icon"></i>
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