<x-admin-layout>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    {{-- <script type="module">
        import 'livewire-sortable' from 'https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js'
    </script> --}}
    @endpush
    @push('styles')
<style>
.list-group .list-group-item.draggable-mirror {
    width: 100%;
    max-width: calc(100% - 390px);
    background-color: #ffb5b5;
}
.list-group .list-group-item.draggable-mirror .list-group-title span::before {
    content: "";
}
.search-results {
    background-color: #f2f2f2;
}
.search-results ul {
    display: inline-block;
    padding-left: 30px;
    margin: 10px 0;
    width: 100%;
    list-style: decimal;
}
.search-results ul li {
    width: 100%;
    margin: 5px 0;
    cursor: pointer;
    position: relative;
    padding-right: 70px;
}
.delete-tags {
    cursor: pointer;
}
</style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Trendet HashTags</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Trendet HashTags</span>
            </li>
        </ul>
    </x-slot>
    <div class="card">
        <div class="card-header"><h5>Ndrysho Trendet për Categorinë "{{ $category->name }}"</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.homesettings.trending.store', $category->id) }}" method="post">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="trending" value="0">
                    <label class="cth-switch cth-switch-success mb-0">
                        <input value="1" type="checkbox" name="trending" id="trending" {{ ($category->trending) ? 'checked' : '' }}>
                        <span></span>
                    </label>
                    <label for="trending" style="position: relative;top: -5px;left: 10px;display: inline-block;">Shfaq këtë Kategori tek Trendet</label>
                </div>
                @livewire('settings.select-trending-cat', ['pid' => $category->id])
                <button type="submit" class="btn btn-primary mt-3">Ruaj</button>
            </form>
        </div>
    </div>
</x-admin-layout>