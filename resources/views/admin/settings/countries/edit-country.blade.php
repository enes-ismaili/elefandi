<x-admin-layout>
    @push('scripts')
        <script src="{{ asset('js/sortable.js') }}"></script>
        <script>
            let citiesList = document.querySelector('.cities-list');
            if(citiesList){
                new Sortable(citiesList, {
                    animation: 150,
                    handle: '.handle',
                    fallbackOnBody: true,
                    swapThreshold: 0.65
                })
            }
        </script>
    @endpush
    @push('styles')
    @endpush
    <a href="{{ route('admin.settings.countries.cities.add', $country->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Shto Qytet për shtetin {{ $country->name }}</a>
    <div class="product-area mt-2">
        <form action="{{route('admin.settings.countries.update', $country->id)}}" method="POST">
            @if(count($country->cities))
            @csrf
            <div class="list-group nested-sortable cities-list">
                @foreach($country->cities()->where('status', '=', 1)->get()->sortBy('corder') as $city)
                    <div class="list-group-item nested-3">
                        <div class="list-group-title" data-id="{{ $city->id }}">
                            <input type="hidden" name="cities[]" value="{{ $city->id }}">
                            <i class="fas fa-arrows-alt handle"></i><span>{{ $city->name }}</span>
                            <div class="categories-right">
                                <a href="{{ route('admin.settings.countries.cities.edit', [$country->id, $city->id]) }}"><i class="fas fa-edit"></i></a>
                                @if(check_permissions('delete_rights'))
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.settings.countries.cities.delete', [$country->id, $city->id]) }}" 
                                    data-text="Ju po fshini Qytetin '{{ $city->name }}'"
                                    data-type="Qytet"><i class="fas fa-trash" class="action-icon"></i>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('cities.*') <span class="text-danger error">{{ $message }}</span>@enderror
            <button type="submit" class="btn btn-primary mt-4">Ruaj</button>
            @endif
        </form>
    </div>
</x-admin-layout>