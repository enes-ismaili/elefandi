<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Footer Link</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.categories.index')}}">Footer Link</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.settings.footer.store', $id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Emri i Linkut</label>
                    <input type="hidden" name="parent" value="{{ $id }}">
                    <input type="text" name="name" class="form-control" id="name" placeholder="Emri" value="{{ old('name') }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="link">Linku</label>
                    <input type="text" name="link" class="form-control" id="link" placeholder="Link" value="{{ old('link') }}">
                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>