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
    <form action="{{route('vendor.coupons.store', ['id' => $coupon->id])}}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Ndrysho Kuponin</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                                <div class="tcenter">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li class="text-danger error">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        <div class="form-group">
                            <label for="code">Kodi i Kuponit</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1">v{{current_vendor()->id }}-</span>
                                </div>
                                <input type="text" name="code" class="form-control" id="code" placeholder="Kuponi" value="{{$coupon->ucode}}">
                            </div>
                            @error('code') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productdesc">Përshkrimi i Kuponit</label>
                            <textarea class="form-control" name="description" id="productdesc" rows="3" placeholder="Përshkrimi">{{$coupon->description}}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group"  x-data="{ selradio:{{ $coupon->type }}, 'utype':{{ $coupon->action }} }">
                            @if($coupon->type == 2)
                                <div class="form-group">
                                    <h6>Kupon për të gjitha produktet e një ose disa Kategorive</h6>
                                </div>
                                <div class="form-group">
                                    <label for="selectMultiMandatory">
                                        Kategoritë
                                        <p class="mb-0">
                                            <small>Zgjidh Kategoritë që i përshaten këtij produkti</small>
                                        </p>
                                    </label>
                                    <select id="selectMultiMandatory" multiple name="categories[]">
                                        @foreach($categories as $parentCategory)
                                            <option value="{{ $parentCategory->id }}" style="font-weight: 700" @if(in_array($parentCategory->id, $selectedCategories)) selected @endif>{{ $parentCategory->name }}</option>
                                            @foreach($parentCategory->children as $category)
                                                <option value="{{ $category->id }}" @if(in_array($category->id, $selectedCategories)) selected @endif>&nbsp;&nbsp;{{ $category->name }}</option>
                                                @foreach($category->children as $subCategory)
                                                    <option value="{{ $subCategory->id }}"  @if(in_array($subCategory->id, $selectedCategories)) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCategory->name }}</option>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('categories') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            @elseif($coupon->type == 3)
                                <div class="form-group">
                                    <h6>Kupon për produkte të zgjedhura</h6>
                                </div>
                                <div class="form-group">
                                    <label for="selectAction">Zgjidh Llojin e Uljes</label>
                                    <select id="selectAction" name="action"  x-on:change="utype = $event.target.value">
                                        <option value="1" @if($coupon->action == 1) selected @endif>Përqindje</option>
                                        <option value="2" @if($coupon->action == 2) selected @endif>Zbritje për produkt</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    @livewire('products.select-products', ['showVendor'=>false, 'vendor_id' => [$selectedVendors], 'product_id'=> $selectedProducts])
                                </div>
                            @else
                                <div class="form-group">
                                    <h6>Kupon për të gjitha produktet e Dyqanit</h6>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="discount">Ulja</label>
                                <div class="input-group mb-3">
                                    <input type="number" name="discount" class="form-control" id="discount" value="{{$coupon->discount}}" min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" x-text="((utype == 2 && selradio === 3) ? '€' : '%')">%</span>
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
                                <input type="checkbox" name="withoffer" id="withoffer" value="1" {{ ($coupon->withoffer) ? 'checked' : '' }}>
                                <span></span>
                            </label>
                            <label for="withoffer" style="position: relative;top: -5px;left: 10px;display: inline-block;">Bëj uljen e kuponit mbi uljen e ofertës nëse ka</label>
                        </div>
                        <div class="form-group">
                            <label for="">Data e fillimit</label>
                            <input class="flatpickr date" type=text placeholder="Data e Fillimit" name="start_date" value="{{ ($coupon->start_date) ? $coupon->start_date : Carbon\Carbon::now() }}">
                        </div>
                        <div class="form-group">
                            <label for="">Data e Përfundimit</label>
                            <input class="flatpickr date tomorrow" type=text placeholder="Data e Përfundimit" name="expire_date" value="{{ ($coupon->expire_date) ? $coupon->expire_date : Carbon\Carbon::tomorrow() }}">
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