<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Tag</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.tags.index')}}">Tag</a>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ selOption: {{ (old('iconType') ? 'false' : 'true') }} }">
        <div class="product-area mt-2">
            <form action="{{ route('admin.products.tags.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="categoryname">Emri i Tag-ut</label>
                    <input type="text" name="name" class="form-control" id="categoryname" placeholder="Emri" value="{{ old('name') }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="categorydesc">Përshkrimi</label>
                    <textarea class="form-control" name="description" id="categorydesc" rows="3" placeholder="Përshkrimi">{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <input type="hidden" value="0" name="iconType">
                    <label class="cth-switch cth-switch-success">
                        <input value="1" type="checkbox" name="iconType" id="iconType" x-on:click="selOption = !selOption" {{ (old('iconType')) ? 'checked' : '' }}>
                        <span></span>
                    </label>
                    <label for="iconType">Ikonë ose Foto</label>
                </div>
                <div class="form-group" x-show="selOption == true">
                    @livewire('icon-picker', ['selectedIcon'=> ((old('icon')) ? old('icon') : 'fas fa-horse')])
                    @error('icon') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" x-show="selOption == false">
                    <label for="logo_path">Foto e Tag-ut</label>
                    @livewire('upload-file', [
                        'inputName' => 'image', 'upload' => 'single', 'exis' =>  ((old('image')) ? old('image') : ''), 'path'=> 'taxonomy/', 'type'=>1, 'deleteF'=>true,
                        'paragraphText' => 'Ngarkoni foton e Tag-ut', 'maxWidth'=>200, 'maxHeight'=>200
                    ])
                    @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>