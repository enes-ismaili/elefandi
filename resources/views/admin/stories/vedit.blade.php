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
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.stories.index')}}">Story</a>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header"><h5>Ndrysho Story</h5></div>
        <div class="card-body">
            <form action="{{ route('vendor.stories.store', $story->id) }}" method="post">
                @csrf
                <div class="stories-story">
                    <div class="dnone" style="display: none">
                        @if(count($story->items) < $slimit)
                            <a href="{{ route('vendor.stories.story.add', $story->id) }}" class="btn btn-primary add-button tright mb-2"><i class="fas fa-plus"></i> Shto Foto/Video</a>
                        @else
                            <p class="text-warning">Ju keni arritur limitin prej <b>{{ $slimit }} Foto/Video</b> për këtë Story (në pritje dhe të aprovuara)<br> Fshini Foto/Video ekzistues për të shtuar të reja</p>
                        @endif
                    </div>
                    <a class="btn btn-primary add-button tright mb-2" href="{{route('vendor.stories.story.add', $story->id)}}">Shto Foto/Video</a>
                    <div class="stories">
                        @if(count($story->items))
                        <div class="stories-list list-group">
                            @foreach ($story->items()->orderBy('created_at', 'DESC')->get() as $item)
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
                                                    <b>Shikime: {{ ($story->fview && $story->fview > 1 )?$item->cview * $story->fview : $item->cview }}</b>, <b>Klikime Butoni: {{ ($story->fclick && $story->fclick > 1 )?$item->clicks * $story->fclick : $item->clicks }}</b>
                                                </div>
                                            </div>
                                        </span>
                                        <div class="categories-right">
                                            <a href="{{ route('vendor.stories.story.edit', [$story->id, $item->id]) }}"><i class="fas fa-edit"></i></a>
                                            <span class="deleteModal action-icon" onclick="deleteModalF(this)"
                                                data-link="{{ route('vendor.stories.story.delete', [$story->id, $item->id]) }}" 
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
                    <div class="form-group mt-5">
                        <label for="name">Emri i Story-t</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Emri i Story-t" value="{{ $story->name }}">
                        @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Ruaj</button>
                @else
                    <p class="mt-5 text-warning">Shtoni të paktën një Foto/Video</p>
                @endif
            </form>
        </div>
    </div>
</x-admin-layout>