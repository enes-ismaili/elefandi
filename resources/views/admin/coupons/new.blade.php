<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script type="module">
            if(document.getElementById('selectMultiMandatory')){
                new SlimSelect({
                    select: '#selectMultiMandatory',
                    placeholder: 'Zgjidhni Kategoritë',
                    closeOnSelect: true,
                    limit: 3,
                    searchText: 'Nuk u gjet asnjë kategori',
                    searchPlaceholder: 'Kërko',
                })
            }
        </script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
        <style>
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
.selected-vendors {
    display: flex;
    flex-wrap: wrap;
}
.selected-vendors > div {
    display: inline-block;
    background-color: #f2f2f2;
    padding: 2px 10px;
    display: flex;
    align-items: center;
    margin-right: 5px;
    margin-bottom: 5px;
}
.selected-vendors > div .close {
    position: relative;
    right: -4px;
    cursor: pointer;
}
.search-results ul li span + span {
    position: absolute;
    top: 0;
    right: 15px;
}
        </style>
    @endpush
    <form action="{{route('admin.coupons.save')}}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Shto Kupon</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="code">Kodi i Kuponit *</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1">codeit-</span>
                                </div>
                                <input type="text" name="code" class="form-control" id="code" placeholder="Kuponi" value="{{ old('code') }}">
                            </div>
                            {{-- <input type="text" name="code" class="form-control" id="code" placeholder="Kuponi" value="{{ old('code') }}"> --}}
                            @error('code') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productdesc">Përshkrimi i Kuponit</label>
                            <textarea class="form-control" name="description" id="productdesc" rows="3" placeholder="Përshkrimi">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="selectAction">Zgjidh Llojin e Uljes *</label>
                            <select id="selectAction" name="action" value="{{ old('action') }}">
                                <option value="1">Përqindje</option>
                                <option value="2">Zbritje për produkt</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discount">Ulja *</label>
                            <input type="number" name="discount" class="form-control" id="discount" value="{{ old('discount') }}" min="0">
                            @error('discount') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Data e fillimit *</label>
                                    <input class="flatpickr date" type=text placeholder="Data e Fillimit" name="start_date" value="{{ (old('start_date')) ? old('start_date') : Carbon\Carbon::now() }}">
                                    @error('start_date') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Data e Përfundimit *</label>
                                    <input class="flatpickr date tomorrow" type=text placeholder="Data e Fillimit" name="expire_date" value="{{ (old('expire_date')) ? old('expire_date') : Carbon\Carbon::tomorrow() }}">
                                    @error('expire_date') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Kategoritë</h5>
                    </div>
                    <div class="card-body">
                        <h6>Zgjidh Produktet ose Kategoritë</h6>
                        <div class="form-group">
                            <label for="selectMultiMandatory">
                                Kategoritë
                                <p class="mb-0">
                                    <small>Zgjidh Kategoritë që i përshaten këtij produkti</small>
                                </p>
                            </label>
                            <select id="selectMultiMandatory" multiple name="categories[]">
                                @foreach($categories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" style="font-weight: 700" @if(old('categories') && in_array($parentCategory->id, old('categories'))) selected @endif>{{ $parentCategory->name }}</option>
                                    @foreach($parentCategory->children as $category)
                                        <option value="{{ $category->id }}" @if(old('categories') && in_array($category->id, old('categories'))) selected @endif>&nbsp;&nbsp;{{ $category->name }}</option>
                                        @foreach($category->children as $subCategory)
                                            <option value="{{ $subCategory->id }}" @if(old('categories') && in_array($subCategory->id, old('categories'))) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCategory->name }}</option>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </select>
                            <p class="mb-0">
                                <small>* Ler bosh për të gjitha</small>
                            </p>
                            @error('categories') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        @livewire('products.select-products', ['vendor_id'=>old('vendors'), 'product_id'=>old('products')])
                        <p class="mb-0">
                            <small>* Në rastet se zgjidhet produktet dhe kategoritë ne kupon do te futen vetem produktet</small>
                        </p>
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-primary ">Ruaj</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>