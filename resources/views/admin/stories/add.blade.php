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
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.stories.index')}}">Story</a>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header"><h5>Shto Story</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.stories.addstore') }}" method="post">
                @csrf
                <p class="text-warning">Foto/Video mund ti hidhni pasi të plotësoni Emrin e Story-t</p>
                <div class="form-group mt-3">
                    <label for="name">Emri i Story-t</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Emri i Story-t" value="{{ old('name') }}">
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="mt-4">
                    <label for="storyStatus">Statusi i Story-t</label>
                    <select name="storyStatus" id="storyStatus">
                        <option value="0">Pritje</option>
                        <option value="1">Aprovuar (Story shfaqet)</option>
                        <option value="2">Rishikim</option>
                        <option value="3">Refuzuar</option>
                    </select>
                    @error('storyStatus') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary mt-3">Ruaj</button>
            </form>
        </div>
    </div>
</x-admin-layout>