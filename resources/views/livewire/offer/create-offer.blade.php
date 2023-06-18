<div>
    <div class="row">
        @error('product_id.*') <span class="text-danger error">{{ $message }}</span>@enderror
        @if($showForm)
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{$offerTitle}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Emri i Ofertës</label>
                            <input type="hidden" name="type" value="{{ $selectedType }}">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Emri" wire:model.defer="name">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        @if($selectedType == 2)
                            <div class="form-group">
                                <label for="selectMultiMandatory">
                                    Kategoritë
                                    <p class="mb-0">
                                        <small>Zgjidh Kategoritë që i përshaten këtij produkti</small>
                                    </p>
                                </label>
                                <select id="selectMultiMandatory" name="category" wire:model.defer="selectedCategories">
                                    <option value="0">Zgjidh Kategorinë</option>
                                    @foreach($categories as $parentCategory)
                                        <option value="{{ $parentCategory->id }}" style="font-weight: 700">{{ $parentCategory->name }}</option>
                                        @foreach($parentCategory->children as $category)
                                            <option value="{{ $category->id }}">&nbsp;&nbsp;{{ $category->name }}</option>
                                            @foreach($category->children as $subCategory)
                                                <option value="{{ $subCategory->id }}">&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCategory->name }}</option>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </select>
                                <p class="mb-0">
                                    <small>* Ler bosh për të gjitha</small>
                                </p>
                                @error('category') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        @elseif($selectedType == 3)
                            <div class="form-group">
                                <label for="selectAction">Zgjidh Produktet</label>
                                <input type="text" wire:model="prod" class="form-control" placeholder="Kërko Produket ...">
                                @if($products)
                                <div class="search-results">
                                    <ul>
                                        @foreach($products as $product)
                                            <li wire:click="addProduct('{{$product->id}}')">
                                                <span>{{ $product->name }}</span>
                                                <span>{{ $product->price }}€</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                @if($selectedProduct)
                                <div class="selected-vendors">
                                    @foreach($selectedProduct as $selProd)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0 h6">{{$selProd['name']}}<span>{{$selProd['price']}}€</span><span class="close" wire:click="rmProduct('{{$selProd['id']}}')">×</span></h5>
                                        </div>
                                        <div class="card-body">
                                            @if(count($selProd['variants']))
                                                @foreach($selProd['variants'] as $variant)
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div>{{$variant['name']}}</div>
                                                    </div>
                                                    <div class="col-9">
                                                        <div class="form-group">
                                                            <input type="hidden" name="product_id[]" class="form-control" value="{{$selProd['id']}}">
                                                            <input type="number" name="product[]" step=".01" min="0" class="form-control sm" placeholder="Çmimi i Ri" value="{{ isset($selProd['v'.$variant['id']]['nprice']) ? $selProd['v'.$variant['id']]['nprice']*1 : '' }}">
                                                            <input type="hidden" name="variant_id[]" class="form-control sm" placeholder="Çmimi i Ri" value="{{ $variant['id'] }}">
                                                            @error('product') <span class="text-danger error">{{ $message }}</span>@enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <p>* Lër bosh ose 0 për të mos e përfshirë variantin në ofertë</p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="form-group">
                                                    <label for="name">Çmimi i Ri</label>
                                                    <input type="hidden" name="product_id[]" class="form-control" value="{{$selProd['id']}}">
                                                    <input type="number" name="product[]" step=".01" min="0"  class="form-control" placeholder="Çmimi" required value="{{ isset($selProd['nprice']) ? $selProd['nprice']*1 : '' }}">
                                                    <input type="hidden" name="variant_id[]" class="form-control sm" placeholder="Çmimi i Ri" value="0">
                                                    @error('product') <span class="text-danger error">{{ $message }}</span>@enderror
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @elseif($selectedType == 4)
                            @if($editRole)
                                <div class="form-group">
                                    <label for="productdesc">Përshkrimi i Ofertës</label>
                                    <textarea class="form-control" name="description" id="productdesc" rows="3" placeholder="Përshkrimi">{{ ($current) ? $current->description : ''}}</textarea>
                                    @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="selectAction">Zgjidh Produktet</label>
                                <input type="text" wire:model="prod" class="form-control" placeholder="Kërko Produket ...">
                                @if($products)
                                <div class="search-results">
                                    <ul>
                                        @foreach($products as $product)
                                            <li wire:click="addProduct('{{$product->id}}')">
                                                <span>{{ $product->name }}</span>
                                                <span>{{ $product->price }}€</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                @if($selectedProduct)
                                <div class="selected-vendors">
                                    @foreach($selectedProduct as $key=>$selvend)
                                        @if(count($selvend))
                                        <div class="select-vendor">
                                            <h3>Dyqani : {{ $selectedProductVendor[$key] }}</h3>
                                            @foreach($selvend as $selProd)
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="mb-0 h6">{{$selProd['name']}}<span>{{$selProd['price']}}€</span><span class="close" wire:click="rmProduct('{{$selProd['id']}}', '{{$key}}')">×</span></h5>
                                                </div>
                                                <div class="card-body">
                                                    @if(count($selProd['variants']))
                                                        @foreach($selProd['variants'] as $variant)
                                                        <div class="row">
                                                            <div class="col-12 col-md-3">
                                                                <div>{{$variant['name']}}</div>
                                                            </div>
                                                            <div class="col-12 col-md-9">
                                                                <div class="form-group">
                                                                    <input type="hidden" name="product_id[]" class="form-control" value="{{$selProd['id']}}">
                                                                    <input type="number" name="product[]" step=".01" min="0" class="form-control sm" placeholder="Çmimi i Ri" value="{{ isset($selProd['v'.$variant['id']]['nprice']) ? $selProd['v'.$variant['id']]['nprice']*1 : '' }}">
                                                                    <input type="hidden" name="variant_id[]" class="form-control sm" placeholder="Çmimi i Ri" value="{{ $variant['id'] }}">
                                                                    @error('product') <span class="text-danger error">{{ $message }}</span>@enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <p>* Lër bosh ose 0 për të mos e përfshirë variantin në ofertë</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    @else
                                                        <div class="form-group">
                                                            <label for="name">Çmimi i Ri</label>
                                                            <input type="hidden" name="product_id[]" class="form-control" value="{{$selProd['id']}}">
                                                            <input type="number" name="product[]" step=".01" min="0" class="form-control" placeholder="Çmimi" required value="{{ isset($selProd['nprice']) ? $selProd['nprice']*1 : '' }}">
                                                            <input type="hidden" name="variant_id[]" class="form-control sm" placeholder="Çmimi i Ri" value="0">
                                                            @error('product') <span class="text-danger error">{{ $message }}</span>@enderror
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Informacionet e Ofertës</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Data e fillimit</label>
                            <input class="flatpickr date" type=text placeholder="Data e Fillimit" name="start_date" value="{{ $startDate }}">
                            @error('start_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="">Data e Përfundimit</label>
                            <input class="flatpickr date tomorrow" type=text placeholder="Data e Përfundimit" name="expire_date" value="{{ $endDate }}">
                            @error('expire_date') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        @if($selectedType == 1 || $selectedType == 2)
                        <div class="form-group">
                            <label for="selectAction">Zgjidh Llojin e Uljes</label>
                            <select id="selectAction" name="action" wire:model.defer="action">
                                <option value="1">Përqindje</option>
                                <option value="2">Zbritje për produkt në €</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="discount">Ulja</label>
                            <input type="number" name="discount" step=".01" min="0" class="form-control" id="discount" required wire:model.defer="discount">
                            @error('discount') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        @endif
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-primary ">Ruaj</button>
                    </div>
                </div>
            </div>
        @else
            <div class="dflex flex-wrap" style="justify-content: space-around;">
                <div class="col-lg-4 col-12">
                    <div class="card select-offer" wire:click="selectType(3)">
                        <div class="card-body">
                            <h5 class="mb-0 h6">Ofertë për produktet</h5>
                            <p>Krijo ofertë për një ose më shumë produkte të zgjedhura</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card select-offer" wire:click="selectType(1)">
                        <div class="card-body">
                            <h5 class="mb-0 h6">Ofertë për gjithë Dyqanin</h5>
                            <p>Krijo ofertë të përbashkët për të gjitha produktet e dyqanit</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card select-offer" wire:click="selectType(2)">
                        <div class="card-body">
                            <h5 class="mb-0 h6">Ofertë për kategoritë</h5>
                            <p>Krijo ofertë për të gjitha produktet e një kategorie</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script type="module">
        document.addEventListener("DOMContentLoaded", () => {
            initDataPicker()
            window.livewire.on('updateOffer', () => {
                initDataPicker()
            });
        });
        function initDataPicker() {
            let flatPickrsDate = document.querySelectorAll('.flatpickr.date');
            flatPickrsDate.forEach(flatPickrDate => {
                flatpickr(flatPickrDate, {
                    altInput: true,
                    altFormat: "j F Y",
                    dateFormat: "Y-m-d",
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            })
        }
    </script>
    </div>
    