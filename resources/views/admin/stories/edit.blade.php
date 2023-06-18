<x-admin-layout>
    @push('scripts')
        <script src="{{ asset('js/sortable.js') }}"></script>
        <script>
            let storiesList = document.querySelector('.stories-list');
            if(storiesList){
                new Sortable(storiesList, {
                    animation: 150,
                    handle: '.handle',
                    fallbackOnBody: true,
                    swapThreshold: 0.65
                });
            }
        </script>
    @endpush
    @push('styles')
<style>
.list-group .list-group-item .list-group-title {
    min-height: 100px;
}
.list-group .list-group-item .list-group-title > span > div {
    position: absolute;
    left: 140px;
    top: 10px;
    margin-right: 45px;
}
.list-group .list-group-item .list-group-title .categories-right {
    float: none;
    top: 0;
    position: absolute;
    right: 0;
    top: 50%;
    transform: translate(0, -60%);
}
</style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Story</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.stories.index')}}">Story</a>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header"><h5>Ndrysho Story</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.stories.store', $story->id) }}" method="post">
                @csrf
                <div class="stories-story">
                    <a class="btn btn-primary add-button tright mb-2" href="{{route('admin.stories.story.add', $story->id)}}">Shto Foto/Video</a>
                    <div class="stories">
                        <p>Ju po shihni vetëm story-it mujore të këtij dyqani</p>
                        @if(count($story->items))
                            <div class="stories-list list-group">
                                @foreach ($story->items()->where('start_story', '>', date('Y-m-1 H:i:s'))->orderBy('created_at', 'DESC')->get() as $item)
                                    @php
                                        $statusText = 'në Pritje';
                                        if($item->cactive == 1){
                                            $statusText = 'Aprovuar';
                                        } elseif($item->cactive == 2){
                                            $statusText = 'Rishikim';
                                        } elseif($item->cactive == 3){
                                            $statusText = 'Refuzuar';
                                        }
                                    @endphp
                                    <div class="list-group-item nested-1"
                                    style="background-color: @if($item->cactive == 1) #28a74554 @elseif($item->cactive == 2) #17a2b854 @elseif($item->cactive == 3) #dc354554 @else #ffc10754 @endif">
                                        <div class="list-group-title" data-id="{{ $item->id }}">
                                            <input type="hidden" name="story_id[]" value="{{ $item->id }}" }}>
                                            <i class="fas fa-arrows-alt handle"></i>
                                            <span>
                                                @if($item->type == 1)
                                                    <img src="{{ asset('photos/story/'.$item->image) }}" width="70" style="max-height: 100px; margin-right: 10px;">
                                                @endif
                                                <div class="d-inline-block">
                                                    <div>
                                                        (Kohëzgjatja: {{ $item->length }}s) <i>, Link: {{ $item->name.' '.$item->link }}</i>,
                                                    </div>
                                                    <div>
                                                        <b>Statusi: {{ $statusText }}</b>
                                                    </div>
                                                    <div>
                                                        <b>Shikime: {{ $item->cview }}</b>, <b>Klikime Butoni: {{ $item->clicks }}</b>
                                                    </div>
                                                    @if(($story->fview && $story->fview >= 1) || ($story->fclick && $story->fclick >= 1))
                                                    <div>
                                                        <b>Shikime Fallco: {{ ($story->fview && $story->fview > 1 )?$item->cview * $story->fview : $item->cview }}</b>, <b>Klikime Butoni  Fallco: {{ ($story->fclick && $story->fclick > 1 )?$item->clicks * $story->fclick : $item->clicks }}</b>
                                                    </div>
                                                    @endif
                                                </div>
                                            </span>
                                            <div class="categories-right">
                                                <a href="{{ route('admin.stories.story.edit', [$story->id, $item->id]) }}"><i class="fas fa-edit"></i></a>
                                                <span class="deleteModal action-icon" onclick="deleteModalF(this)"
                                                    data-link="{{ route('admin.stories.story.delete', [$story->id, $item->id]) }}" 
                                                    data-text="Ju po fshini Foto/Video të Story-in '{{$story->name}}'"
                                                    data-type="Story Foto/video"><i class="fas fa-trash" class="action-icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @if(count($story->items))
                    {{-- <div class="form-group mt-5">
                        <label for="name">Emri i Story-t</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Emri i Story-t" value="{{ $story->name }}">
                        @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div> --}}
                    <div class="form-group">
                        <label for="fview">Shumfisho Shikimet për Foto/Video e këtij Story</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">*</span>
                            </div>
                            <input type="number" name="fview" class="form-control" id="fview" placeholder="Lër 0 ose bosh për jo aktive" value="{{ $story->fview }}" step=".1" min="0">
                        </div>
                        <p>* Psh 2 shikime * 5 dhe dyqanit do ti dalin 10 shikime</p>
                    </div>
                    <div class="form-group">
                        <label for="fclick">Shumfisho Klikimet për Foto/Video e këtij Story</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">*</span>
                            </div>
                            <input type="number" name="fclick" class="form-control" id="fclick" placeholder="Lër 0 ose bosh për jo aktive" value="{{ $story->fclick }}" step=".1" min="0">
                        </div>
                        <p>* Psh 2 klikime * 5 dhe dyqanit do ti dalin 10 klikime</p>
                    </div>
                    {{-- <div class="mt-4">
                        <label for="storyStatus">Statusi i Story-t</label>
                        <select name="storyStatus" id="storyStatus">
                            <option value="0" @if($story->cactive == 0) selected @endif>Pritje</option>
                            <option value="1" @if($story->cactive == 1) selected @endif>Aprovuar (Story shfaqet)</option>
                            <option value="2" @if($story->cactive == 2) selected @endif>Rishikim</option>
                            <option value="3" @if($story->cactive == 3) selected @endif>Refuzuar</option>
                        </select>
                        @error('storyStatus') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div> --}}
                    <button type="submit" class="btn btn-primary mt-3">Ruaj</button>
                @else
                    <p class="mt-5">Nuk ka Foto/Video për këtë grup storish</p>
                @endif
            </form>
        </div>
    </div>
</x-admin-layout>