<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Kategorite</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.categories.index')}}">Kategoritë</a>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ selOption: {{ ($scategory->icon) ? 'true' : 'false' }} }">
        <div class="product-area mt-2">
            <form action="{{ route('admin.products.categories.update', $scategory->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="categoryname">Emri i Kategorisë</label>
                    <input type="text" name="name" class="form-control" id="categoryname" placeholder="Emri" value="{{ $scategory->name }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="categorydesc">Përshkrimi</label>
                    <textarea class="form-control" name="description" id="categorydesc" rows="3" placeholder="Përshkrimi">{{ $scategory->description }}</textarea>
                    @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="parentcategory">Kategoria Prind</label>
                    <select class="form-control" name="parent" id="parentcategory">
                        <option value="0" style="color: #000;font-weight: 700;">Asnjë</option>
                        @foreach($categories as $parentCategory)
                            <option value="{{ $parentCategory->id }}" style="color: #000;font-weight: 700;" @if($parentCategory->id == $scategory->parent) selected @endif>{{ $parentCategory->name }}</option>
                                @foreach($parentCategory->children as $category)
                                    <option value="{{ $category->id }}" @if($category->id == $scategory->parent) selected @endif>&nbsp;&nbsp;{{ $category->name }}</option>
                                @endforeach
                        @endforeach
                    </select>
                    @error('parent') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <input type="hidden" value="0" name="iconType">
                    <label class="cth-switch cth-switch-success">
                        <input value="1" type="checkbox" name="iconType" id="iconType" x-on:click="selOption = !selOption" {{ ($scategory->image) ? 'checked' : '' }}>
                        <span></span>
                    </label>
                    <label for="iconType">Ikonë ose Foto</label>
                </div>
                <div class="form-group" x-show="selOption == true">
                    @livewire('icon-picker', ['selectedIcon'=> (($scategory->icon) ? $scategory->icon : 'fas fa-horse')])
                    @error('icon') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" x-show="selOption == false">
                    <label for="logo_path">Foto e Kategorisë</label>
                    @livewire('upload-file', [
                        'inputName' => 'image', 'upload' => 'single', 'exis' =>  (($scategory->image) ? $scategory->image : ''), 'path'=> 'taxonomy/', 'type'=>1, 'deleteF'=>true,
                        'paragraphText' => 'Ngarkoni foton e kategorisë', 'maxWidth'=>200, 'maxHeight'=>200
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