<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ngjyrat</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.colors.index')}}">Ngjyrat</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.products.colors.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="categoryname">Emri i Ngjyrës</label>
                    <input type="text" name="name" class="form-control" id="categoryname" placeholder="Emri" value="{{ old('name') }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>