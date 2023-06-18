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
        <div class="card-header"><h5>Ndrysho shfaqjen e Kategorisë "{{ $category->name }}" në Kreun</h5></div>
        <div class="card-body">
            <form action="{{ route('admin.homesettings.categories.store', $category->id) }}" method="post">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="home" value="0">
                    <label class="cth-switch cth-switch-success mb-0">
                        <input value="1" type="checkbox" name="home" id="home" {{ ($category->home) ? 'checked' : '' }}>
                        <span></span>
                    </label>
                    <label for="home" style="position: relative;top: -5px;left: 10px;display: inline-block;">Shfaq këtë Kategori në Kreun</label>
                </div>
                <div class="category-child">
                    @livewire('settings.select-category-children', ['pid' => $category->id])
                </div>
                <div class="category-slider">
                    <a class="btn btn-primary add-button tright" href="{{route('admin.homesettings.categories.slider', $category->id)}}">Shto Slider</a>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Linku</th>
                                    <th>Shfaq</th>
                                    <th data-sortable="false" class="action-icons" width="130">Veprime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->sliders()->orderBy('corder')->get() as $slider)
                                    <tr>
                                        <td>{!! ($slider->image) ? '<img src="'.asset('/photos/category/'.$slider->image).'">' : '' !!}</td>
                                        <td>{{ ($slider->link) ? $slider->link : '' }}</td>
                                        <td>{{ ($category->home) ? 'Po' : 'Jo' }}</td>
                                        <td class="action-icons">
                                            <a href="{{ route('admin.homesettings.categories.editslider', [$category->id, $slider->id]) }}" class="action-icon" title="Ndrysho"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                            <span class="deleteModal action-icon" title="Fshi" onclick="deleteModalF(this)"
                                                data-link="{{ route('admin.homesettings.categories.deleteslider', [$category->id, $slider->id]) }}" 
                                                data-text="Ju po fshini Sliderin me link '{{$slider->link}}'"
                                                data-type="Slider"><i class="fas fa-trash" class="action-icon"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Ruaj</button>
            </form>
        </div>
    </div>
</x-admin-layout>