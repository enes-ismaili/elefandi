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
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Footer Kolona {{ $colNr }}</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.settings.countries.index')}}">Footer Kolona {{ $colNr }}</a>
            </li>
        </ul>
    </x-slot>
    <a href="{{ route('admin.settings.footer.store', $colNr) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Shto Menu</a>
    <div class="product-area mt-2">
        <form action="{{route('admin.settings.footer.order', $colNr)}}" method="POST">
            @if(count($footer))
            @csrf
            <div class="list-group nested-sortable cities-list">
                @foreach($footer as $link)
                    <div class="list-group-item nested-3">
                        <div class="list-group-title" data-id="{{ $link->id }}">
                            <input type="hidden" name="links[]" value="{{ $link->id }}">
                            <i class="fas fa-arrows-alt handle"></i><span>{{ $link->name.' ('.$link->link.')' }}</span>
                            <div class="categories-right">
                                <a href="{{ route('admin.settings.footer.edit', [$colNr, $link->id]) }}"><i class="fas fa-edit"></i></a>
                                <span class="deleteModal action-icon"  title="Fshi" onclick="deleteModalF(this)"
                                    data-link="{{ route('admin.settings.footer.delete', [$colNr, $link->id]) }}" 
                                    data-text="Ju po fshini Link '{{ $link->name }}'"
                                    data-type="Link"><i class="fas fa-trash" class="action-icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('links.*') <span class="text-danger error">{{ $message }}</span>@enderror
            <button type="submit" class="btn btn-primary mt-4">Ruaj</button>
            @endif
        </form>
    </div>
</x-admin-layout>