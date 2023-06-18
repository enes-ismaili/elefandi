<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Foto/Video Story</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.stories.index')}}">Story-t</a>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ utype: {{ (($story->type) ? $story->type : 1) }} }">
        <div class="product-area mt-2">
            <form action="{{ route('vendor.stories.story.update', [$stories->id, $story->id]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="storyType">Lloji</label>
                    <select name="storyType" id="storyType" x-on:change="utype = $event.target.value">
                        <option value="1" {{ ($story->type == 1)? 'selected' : '' }}>Foto</option>
                        <option value="2" {{ ($story->type == 2)? 'selected' : '' }}>Video</option>
                    </select>
                    @error('storyType') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="name">Emri i butonit të Linkut</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Emri i butonit" value="{{ $story->name }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="link">Linku</label>
                    <input type="text" name="link" class="form-control" id="link" placeholder="Linku" value="{{ $story->link }}">
                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="duration">Kohezgjatja</label>
                    <input type="number" name="duration" class="form-control" id="duration" placeholder="Kohezgjatja" value="{{ $story->length }}" min="1" max="15">
                    @error('duration') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" x-show="utype == 1">
                    <label for="image">Foto</label>
                    @livewire('upload-file', [
                        'inputName' => 'image', 'upload' => 'single', 'exis' =>  (($story->type == 1 && $story->image) ? $story->image : ''), 'path'=> 'story/', 'type'=>2, 'deleteF'=>false,
                        'paragraphText' => 'Ngarkoni foton (rezulucioni maksimal: 1000px x 1000px)', 'maxWidth'=>1500, 'maxHeight'=>1000
                    ])
                    @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group" x-show="utype == 2">
                    <label for="video">Video</label>
                    @livewire('upload-file', [
                        'inputName' => 'video', 'upload' => 'single', 'exis' =>  (($story->type == 2 && $story->image) ? $story->image : ''), 'path'=> 'story/', 'type'=>3, 'deleteF'=>false,
                        'paragraphText' => 'Ngarkoni videon (rezulucioni maksimal: 1000px x 1000px)', 'maxWidth'=>1500, 'maxHeight'=>1000
                    ])
                    @error('video') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <p class="text-warning">Kujdes! Ndryshimi i Linkut ose Fotos/Videos e çon për aprovim tek administratorët</p>
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>