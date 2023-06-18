<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
<style>
</style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Kategoritë Slider</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Kategoritë Slider</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header"><h5>Shto Slider</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.homesettings.categories.addslider', $category->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="image">Imazhi</label>
                            @livewire('upload-file', [
                                'inputName' => 'image', 'upload' => 'single', 'exis' => ((old('image'))?old('image'):''), 'path'=> 'category/', 'type'=>1, 'deleteF'=>false, 
                                'paragraphText' => 'Ngarkoni foto slider', 'maxWidth'=>1000, 'maxHeight'=>500, 'uid' => 1
                            ])
                            @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="link">Linku</label>
                                    <input type="text" name="link" class="form-control" id="link" placeholder="Linku" value="{{ old('link') }}">
                                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="corder">Renditja</label>
                            <input type="number" name="corder" class="form-control" id="corder" placeholder="Renditja" value="{{ (old('corder')) ?? '9' }}">
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