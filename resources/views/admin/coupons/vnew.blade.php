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
.cth-switch-el {
    overflow: hidden;
    max-height: 0;
    transition: all .2s ease;
    margin: 0;
}
.cth-switch-el.show {
    max-height: 999px;
    transition: all .2s ease;
    padding-top: 10px;
    overflow: unset;
    margin-bottom: 15px;
}
        </style>
    @endpush
    <form action="{{route('vendor.coupons.save')}}" method="POST">
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
                                  <span class="input-group-text" id="basic-addon1">v{{current_vendor()->id }}-</span>
                                </div>
                                <input type="text" name="code" class="form-control" id="code" placeholder="Kuponi" value="{{ old('code') }}">
                            </div>
                            @error('code') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productdesc">Përshkrimi i Kuponit</label>
                            <textarea class="form-control" name="description" id="productdesc" rows="3" placeholder="Përshkrimi">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group"  x-data="{ openc: @if(old('coupontype')) true @else false @endif, selradio:1, 'utype':1 }">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="coupontype" id="coupontype1" value="1" x-on:click="selradio = 1" checked>
                                    <label class="form-check-label" for="coupontype1">Për të gjitha produktet e Dyqanit</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="coupontype" id="coupontype2" value="2" x-on:click="selradio = 2">
                                    <label class="form-check-label" for="coupontype2">Për të gjitha produktet e një ose disa Kategorive</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="coupontype" id="coupontype3" value="3" x-on:click="selradio = 3">
                                    <label class="form-check-label" for="coupontype3">Zgjidh Produktet</label>
                                </div>
                            </div>
                            <div class="form-group cth-switch-el" :class="{ 'show ': selradio === 2 }">
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
                                                <option value="{{ $subCategory->id }}"  @if(old('categories') && in_array($subCategory->id, old('categories'))) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCategory->name }}</option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('categories') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            <div class="cth-switch-el" :class="{ 'show ': selradio === 3 }">
                                @livewire('products.select-products', ['showVendor'=>false, 'product_id'=>old('products')])
                            </div>
                            <div class="form-group mt-4" x-show="selradio === 3">
                                <label for="selectAction">Zgjidh Llojin e Uljes</label>
                                <select id="selectAction" name="action" value="{{ old('action') }}" x-on:change="utype = $event.target.value">
                                    <option value="1">Përqindje</option>
                                    <option value="2" >Zbritje për produkt</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="discount" x-text="((selradio === 3) ? 'Ulja' : 'Ulja ne Perqindje')"></label>
                                <div class="input-group mb-3">
                                    <input type="number" name="discount" class="form-control" id="discount" value="{{ old('discount') }}" min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text"  x-text="((utype == 2 && selradio === 3) ? '€' : '%')">%</span>
                                    </div>
                                </div>
                                @error('discount') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Detajet</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <input type="hidden" name="withoffer" value="0">
                            <label class="cth-switch cth-switch-success mb-0">
                                <input type="checkbox" name="withoffer" id="withoffer" value="1">
                                <span></span>
                            </label>
                            <label for="withoffer" style="position: relative;top: -5px;left: 10px;display: inline-block;">Bëj uljen e kuponit mbi uljen e ofertës nëse ka</label>
                        </div>
                        <div class="form-group">
                            <label for="">Data e fillimit</label>
                            <input class="flatpickr date" type=text placeholder="Data e Fillimit" name="start_date" value="{{ (old('start_date')) ? old('start_date') : Carbon\Carbon::now() }}">
                            @error('start_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="">Data e Përfundimit</label>
                            <input class="flatpickr date tomorrow" type=text placeholder="Data e Përfundimit" name="expire_date" value="{{ Carbon\Carbon::tomorrow() }}">
                            @error('expire_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                            
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-primary ">Ruaj</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>