<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
.feature-image {
    overflow: hidden;
    max-height: 0;
    transition: all .2s ease;
    margin-left: 5px;
}
.feature-image.show {
    max-height: 999px;
    transition: all .2s ease;
    padding-top: 10px;
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
        <div class="card-header"><h5>Shto Veçori</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.homesettings.storeFeatures') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="name">Titulli</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Titulli" value="{{ old('name') }}">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description">Përshkrimi</label>
                            <textarea class="form-control" name="description" id="description" rows="3" placeholder="Përshkrimi">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="feature-images row col-12" x-data="{ imageIcon: false }">
                        <div class="col-12">
                            <label class="cth-switch cth-switch-success mb-0">
                                <input value="1" type="checkbox" name="imageFeature" id="imageFeature" checked x-on:click="imageIcon = !imageIcon">
                                <span></span>
                            </label>
                            <label for="imageFeature" style="position: relative;top: -5px;left: 10px;display: inline-block;">Imazh ose Ikonë</label>
                        </div>
                        <div class="col-12 feature-image"  :class="{ 'show ': imageIcon === true }">
                            <div class="form-group">
                                <label for="image">Imazhi</label>
                                @livewire('upload-file', [
                                    'inputName' => 'image', 'upload' => 'single', 'exis' => '', 'path'=> 'images/', 'type'=>1, 'deleteF'=>false, 
                                    'paragraphText' => 'Ngarkoni foto', 'maxWidth'=>200, 'maxHeight'=>200, 'uid' => 1
                                ])
                            </div>
                        </div>
                        <div class="col-12 feature-image"  :class="{ 'show ': imageIcon === false }">
                            <div class="form-group">
                                <label for="image">Ikona</label>
                                @livewire('icon-picker', ['selectedIcon'=> ''])
                            </div>
                        </div>
                        @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                        @error('icon') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="corder">Renditja</label>
                            <input type="number" name="corder" class="form-control" id="corder" placeholder="Renditja" value="{{ (old('corder')) ?? '4' }}">
                            @error('corder') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Ruaj</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>