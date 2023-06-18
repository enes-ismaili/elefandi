<x-admin-layout>
    @push('scripts')
        <script src="{{ asset('js/sortable.js') }}"></script>
        <script>
            let nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
            for (var i = 0; i < nestedSortables.length; i++) {
                new Sortable(nestedSortables[i], {
                    animation: 150,
                    handle: '.handle',
                    fallbackOnBody: true,
                    swapThreshold: 0.65
                });
            }
            let categoriesMinimize = document.querySelectorAll('.list-group-title .toogle-category');
            // minimize
            categoriesMinimize.forEach(category => {
                category.addEventListener('click', (e)=>{
                    let parentElement = e.target.parentElement.parentElement;
                    let childrenElement = e.target.parentElement.parentElement.nextElementSibling;
                    parentElement.classList.toggle('category-collapse');
                    childrenElement.classList.toggle('category-collapse');
                });
            });
            let currentOption = 'close';
            let toggleCategories = document.querySelector('#collapse-all');
            toggleCategories.addEventListener('click', (e)=>{
                let allCategories = document.querySelectorAll('.list-group-title .toogle-category');
                allCategories.forEach(category => {
                    let parentElement = category.parentElement.parentElement;
                    let childrenElement = category.parentElement.parentElement.nextElementSibling;
                    if(currentOption == 'close'){
                        if(!parentElement.classList.contains('category-collapse')){
                            parentElement.classList.toggle('category-collapse');
                        }
                        if(!childrenElement.classList.contains('category-collapse')){
                            childrenElement.classList.toggle('category-collapse');
                        }
                    } else {
                        if(parentElement.classList.contains('category-collapse')){
                            parentElement.classList.toggle('category-collapse');
                        }
                        if(childrenElement.classList.contains('category-collapse')){
                            childrenElement.classList.toggle('category-collapse');
                        }
                    }
                })
                if(currentOption == 'close'){
                    currentOption = 'open';
                    toggleCategories.innerHTML = '<i class="fas fa-expand-alt"></i> Hap të Gjitha';
                } else {
                    currentOption = 'close';
                    toggleCategories.innerHTML = '<i class="fas fa-compress-alt"></i> Mbyll të Gjitha';
                }
            });
            let getAllCategories = document.querySelectorAll('.list-group-item.nested-1');
            // console.log(getAllCategories);
            let allCategoriesObject = [];
            setTimeout( function(){
                getAllCategories.forEach(category => {
                    let thisCategory = Array();
                    if(category.children.length > 1){
                        let childCat = category.children[1];
                        if(childCat.children.length){
                            Array.from(childCat.children).forEach(categoryChild => {
                                thisCategory.push(categoryChild.children[0].dataset.id);
                            })
                        }
                    }
                    allCategoriesObject.push([category.children[0].dataset.id, thisCategory]);
                });
            }, 200)
            let saveCategory = document.querySelector('#save-categories');
            saveCategory.addEventListener('click', e=>{
                let allCategoriesObject1 = [];
                let getAllCategories1 = document.querySelectorAll('.list-group-item.nested-1');
                getAllCategories1.forEach(category => {
                    let thisCategory = Array();
                    if(category.children.length > 1){
                        let childCat = category.children[1];
                        Array.from(childCat.children).forEach(categoryChild => {
                            let thisSubCategory = Array();
                            if(categoryChild.children.length > 1){
                                let chilsubdCat = category.children[1];
                                Array.from(chilsubdCat.children).forEach(subCategoryChild => {
                                    thisSubCategory.push(subCategoryChild.children[0].dataset.id);
                                });
                            }
                            thisCategory.push([categoryChild.children[0].dataset.id, thisSubCategory]);
                        })
                    }
                    allCategoriesObject1.push([category.children[0].dataset.id, thisCategory]);
                });
                let AllCategories = JSON.stringify(allCategoriesObject1);
                let url = '{{route('admin.products.categories.savelist')}}'
                let formData = new FormData();
                formData.append("categories", AllCategories);
                fetch(url, {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    let randomC = Math.floor(Math.random() * 500);
                    console.log('Success:', data);
                    let pageMain = document.querySelector('.page-main');
                    let html = `<div class="flash-notifications load flash-${randomC}" >
                            <div class="alert fade alert-success alert-dismissible show">
                            <button type="button" class="close font__size-18" data-dismiss="alert">
                                <span aria-hidden="true"><i class="fa fa-times"></i></span><span class="sr-only">Close</span>
                            </button>
                            <i class="start-icon far fa-check-circle faa-tada animated"></i>
                            <b>Kategoritë u ruajtën me sukses.</b>
                        </div>
                    </div>`;
                    pageMain.insertAdjacentHTML('beforeend', html);
                    setTimeout(() => {
                        let cNotification = document.querySelector('.flash-notifications.flash-'+randomC);
                        cNotification.remove();
                    }, 1000);
                })
                .catch((e) => {
                    console.log(e);
                });
            })
        </script>
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
    <div x-data="{ open: false }">
        <a href="{{ route('admin.products.categories.add') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Shto Kategori</a>
        <button type="button" id="collapse-all" class="btn btn-warning collapse-all float-right"><i class="fas fa-compress-alt"></i> Mbyll të Gjitha</button>
        <div class="product-area mt-2">
            <div>
                <div class="row">
                    <div class="col-lg-12">
                        <div id="nestedDemo" class="list-group nested-sortable sortable-1">
                            @foreach($categories as $parentCategory)
                                <div class="list-group-item nested-1">
                                    <div class="list-group-title" data-id="{{ $parentCategory->id }}">
                                        <i class="fas fa-arrows-alt handle"></i><span class="name">{{ $parentCategory->name }}</span>
                                        <div class="categories-right">
                                            <a href="{{ route('admin.products.categories.edit', $parentCategory->id) }}"><i class="fas fa-edit"></i></a>
                                            @if(check_permissions('delete_rights'))
                                            <span class="deleteModal action-icon" onclick="deleteModalF(this)"
                                                data-link="{{ route('admin.products.categories.delete', $parentCategory->id) }}" 
                                                data-text="Ju po fshini Kategorinë e '{{ $parentCategory->name }}'"
                                                data-type="Kategori"><i class="fas fa-trash" class="action-icon"></i>
                                            </span>
                                            @endif
                                            @if(count($parentCategory->children))
                                                <div class="toogle-category"><i class="fas fa-compress-alt"></i></div>
                                            @endif
                                        </div>
                                    </div>
                                    @if(count($parentCategory->children))
                                        <div class="list-group nested-sortable sortable-2">
                                            @foreach($parentCategory->children->sortBy('corder') as $category)
                                                <div class="list-group-item nested-2">
                                                    <div class="list-group-title" data-id="{{ $category->id }}">
                                                        <i class="fas fa-arrows-alt handle"></i><span class="name">{{ $category->name }}</span>
                                                        <div class="categories-right">
                                                            <a href="{{ route('admin.products.categories.edit', $category->id) }}"><i class="fas fa-edit"></i></a>
                                                            @if(check_permissions('delete_rights'))
                                                            <span class="deleteModal action-icon" onclick="deleteModalF(this)"
                                                                data-link="{{ route('admin.products.categories.delete', $category->id) }}" 
                                                                data-text="Ju po fshini Kategorinë e '{{ $category->name }}'"
                                                                data-type="Kategori"><i class="fas fa-trash" class="action-icon"></i>
                                                            </span>
                                                            @endif
                                                            @if(count($category->children))
                                                                <div class="toogle-category"><i class="fas fa-compress-alt"></i></div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if(count($category->children))
                                                        <div class="list-group nested-sortable sortable-3">
                                                            @foreach($category->children->sortBy('corder') as $subCategory)
                                                                <div class="list-group-item nested-3">
                                                                    <div class="list-group-title" data-id="{{ $subCategory->id }}">
                                                                        <i class="fas fa-arrows-alt handle"></i><span class="name">{{ $subCategory->name }}</span>
                                                                        <div class="categories-right">
                                                                            <a href="{{ route('admin.products.categories.edit', $subCategory->id) }}"><i class="fas fa-edit"></i></a>
                                                                            @if(check_permissions('delete_rights'))
                                                                            <span class="deleteModal action-icon" onclick="deleteModalF(this)"
                                                                                data-link="{{ route('admin.products.categories.delete', $subCategory->id) }}" 
                                                                                data-text="Ju po fshini Kategorinë e '{{ $subCategory->name }}'"
                                                                                data-type="Kategori"><i class="fas fa-trash" class="action-icon"></i>
                                                                            </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-12 mt-2 ml-1">
                        <div id="save-categories" class="btn btn-primary ">Ruaj</div>
                    </div>
                </div>
            </div>
            <div class="modal fade" :class="{ 'show  dshow': open === true }" x-show="open" x-show="open" @click.away="open = false">
                <div class="modal_bg" @click="open = false"></div>
                <div class="modal-dialog modal-lg" modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Shto Kategori të re</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"  @click="open = false">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="content-area">
                                @livewire('products.add-category')
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="open = false">Close</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Ruaj</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>