<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        {{-- <script src="{{ asset('js/input-tags.js') }}"></script> --}}
        <script src="{{ asset('js/tagger.js') }}"></script>
        <script src="{{ mix('js/posts.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.8.2/tinymce.min.js"></script>
        <script>
            var input = document.querySelector('[name="tags"]');
            var tags = tagger(input, {
                allow_duplicates: false,
                allow_spaces: true,
                wrap: true,
                completion: {
                    list: []
                }
            });
            document.querySelector('.add_field').onclick = addSpecification;
            let fieldHTML = `<div class="row"><div class="col-5"><div class="form-group"><input class="form-control" type="text" name="customfield_name[]" placeholder="Emri i Fushes" required></div></div>
                <div class="col-5"><div class="form-group"><input class="form-control" type="text" name="customfield_value[]" placeholder="Vlera i Fushes"  required></div></div>
                <div class="col-2"><div class="form-group"><a href="javascript:void(0);" class="btn remove_button">Hiq</a></div></div></div>`;
            let x = 1; 
            let wrapperSpec = document.querySelector('#customfield');
            let allRows = wrapperSpec.querySelectorAll('.remove_button')
            allRows.forEach((t)=>{
                t.onclick = removeSpecification
            })
            function addSpecification(e) {
                if(x < 20){ 
                    x++;
                    wrapperSpec.insertAdjacentHTML('beforeend', fieldHTML);
                }
                let allRows = wrapperSpec.querySelectorAll('.remove_button')
                allRows.forEach((t)=>{
                    t.onclick = removeSpecification
                })
            }
            function removeSpecification(e) {
                e.preventDefault();
                console.log(e);
                e.target.closest('.row').remove();
                x--;
            }
            // // let allTags = document.querySelectorAll('.customer_choice_options .col-md-8 input');
            // // allTags.forEach((e)=> {
            // //     var tags = tagger(e, {
            // //         allow_duplicates: false,
            // //         allow_spaces: true,
            // //         wrap: true,
            // //         completion: {
            // //             list: []
            // //         }
            // //     });
            // //     // getAllTags();
            // // })
            window.selectedColors = ["{!! collect($colors)->implode('", "') !!}"]
            window.selectedAttribute = ["{!! collect($attributes)->implode('", "') !!}"]
            @if($product->variants->count())
                @php
                    $variantsArr = [];
                    $variantsArr2 = [];
                    foreach($product->variants as $item) {
                        $thisVar = [
                            'id' => $item->slug,
                            'name' => $item->name,
                            'price' => ($item->price && $item->price > 0)? $item->price :'',
                            'sku' => ($item->sku)??'',
                            'qty' => ($item->stock && $item->stock > 0)? $item->stock : '',
                            'img' => ($item->image)??'',
                        ];
                        $variantsArr2['v'.$item->slug] = $thisVar;
                        array_push($variantsArr, $thisVar);
                    }
                @endphp
                window.existVariants = {!! collect($variantsArr2) !!}
            @else
                window.existVariants = []
            @endif

            function delete_variant(e){
                let thisRow = e.parentElement.parentElement;
                let thisImage = thisRow.querySelector('.selected-files').value;
                thisRow.remove();
            }

            window.uploadVariantImage = (input, file) => {
                let url = "https://new57.elefandi.com/upload/image";
                let formData = new FormData();
                formData.append("file", file);
                fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    previewFile(input, data.file)
                    input.parentElement.querySelector('.selected-files').value = data.name;
                })
                .catch((e) => {
                    console.log(e);
                });
            }
			
			function removeVariantImage(e){
				let parentElements = e.parentElement;
				let parentElementInp = parentElements.parentElement;
				let selectedImage = parentElementInp.querySelector('.svariants_image');
				parentElements.innerHTML = '';
				parentElementInp.classList.remove('upload');
				selectedImage.value = '';
            }
            
            function previewFile(input, file) {
                const img = document.createElement("img");
                img.src = file;
                input.previousElementSibling.insertAdjacentHTML('beforeend', '<div class="remove-image" onclick="removeVariantImage(this)"><i class="fas fa-times"></i></div>')
                input.previousElementSibling.appendChild(img);
                input.parentElement.classList.add('upload')
            }
        </script>
        <script>
            tinymce.init({
                selector: 'textarea#product-description',
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                imagetools_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar1: 'formatselect | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | table link pagebreak hr fullscreen preview',
                toolbar2: 'undo redo | fontsizeselect | outdent indent | forecolor backcolor | superscript subscript removeformat charmap emoticons',
                toolbar_sticky: true,
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: true,
                link_list: [
                    { title: 'My page 1', value: 'https://www.tiny.cloud' },
                    { title: 'My page 2', value: 'http://www.moxiecode.com' }
                ],
                image_list: [
                    { title: 'My page 1', value: 'https://www.tiny.cloud' },
                    { title: 'My page 2', value: 'http://www.moxiecode.com' }
                ],
                image_class_list: [
                    { title: 'None', value: '' },
                    { title: 'Some class', value: 'class-name' }
                ],
                importcss_append: true,
                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
                    }
                    if (meta.filetype === 'image') {
                        callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
                    }
                    if (meta.filetype === 'media') {
                        callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
                    }
                },
                block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
                fontsize_formats: '8px 10px 12px 14px 16px 18px 20px 24px 36px 48px',
                template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
                template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
                height: 300,
                image_caption: true,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                quickbars_insert_toolbar: false,
                noneditable_noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'copy cut paste removeformat link lists image imagetools table',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });
        </script>
    @endpush
    @push('styles')
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
        <link  rel="stylesheet" href="{{ mix('css/post.css') }}">
        <style>
.shipping-product {
	overflow: hidden;
    max-height: 0;
    transition: all .2s ease;
    margin-left: 5px;
}
.shipping-product.show {
	max-height: 999px;
    transition: all .2s ease;
    padding-top: 10px;
}
        </style>
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ndrysho Produkt</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.products.show')}}">Ndrysho Produkt</a>
            </li>
        </ul>
    </x-slot>
    <form action="{{route('admin.products.update', ['id' => $product->id])}}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Informacionet e Produktit</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="productname">Emri</label>
                            <input type="text" name="name" class="form-control" id="productname" placeholder="Emri" value="{{$product->name}}">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="product-description">Përshkrimi</label>
                            <textarea class="form-control" name="description" id="product-description" rows="3" placeholder="Përshkrimi">{{$product->description}}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Specifikimet e Produktit</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="product_weight">Pesha e Produktit</label>
                            <input type="text" name="weight" class="form-control" id="product_weight" placeholder="Psh: 1 kg" value="{{ $product->weight }}">
                            @error('weight') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="product_size">Madhësia e produktit (p.sh 10x20x30)</label>
                            <input type="text" name="size" class="form-control" id="product_size" placeholder="Psh: 10x20x30" value="{{ $product->size }}">
                            @error('size') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="row" x-data="{ openPersonalize: @if($product->personalize) true @else false @endif }">
                            <label class="col-md-12">Mundësia e Personalizimit</label>
                            <div class="form-group col-md-12 ">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input type="checkbox" name="activepersonalize" x-on:click="openPersonalize = !openPersonalize" @if($product->personalize) checked @endif>
                                    <span></span>
                                </label>
                                <div style="position: relative;top: -5px;left: 10px;display: inline-block;">Mundesia nga ana e përdoruesit për të personalizuar këtë produkt (Psh. ngjyrat, emri sipër produktit, etj)</div>
                            </div>
                            <div class="form-group col-md-12 personalize-title" :class="{ 'show ': openPersonalize === true }">
                                <input class="form-control" type="text" name="personalizetitle" placeholder="Shkruani llojin e personalizmit që do ti shfaqet përdoruesit" value="{{ $product->personalize }}">
                            </div>
                        </div>
                        <label for="">Shtoni më shumë specifikime</label>
                        <div id="customfield">
							@foreach($product->specification as $specification)
                            <div class="row">
                                <div class="col-5">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="customfield_name[]" placeholder="Emri i Fushes" required value="{{ $specification->name }}">
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="customfield_value[]" placeholder="Vlera i Fushes" required value="{{ $specification->value }}">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <a href="javascript:void(0);" class="btn remove_button">Hiq</a>
                                    </div>
                                </div>
                            </div>
							@endforeach
						</div>
                        <div class="col-12">
                            <div class="form-group">
                                <a href="javascript:void(0);" class="btn add_field float-right">Shto fushë të veçantë</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Variantet e Produktit</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="selectColors">Ngjyrat
                                <p class="mb-0">
                                    <small>Zgjidh Ngjyrat që i përshaten këtij produkti</small>
                                </p>
                            </label>
                            <select id="selectColors" multiple name="variant_colors[]">
                                @foreach($colorsList as $color)
                                    <option value="{{$color->id}}" class="{{$color->slug}}" @if(in_array($color->id, $colors)) selected @endif>{{$color->name}}</option>
                                @endforeach
                            </select>
                            @error('variant_colors') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="cth-switch cth-switch-success mb-0">
                                <input value="0" type="checkbox" name="colors_active" id="colors_active" @if(count($attributes) > 0) checked @endif>
                                <span></span>
                            </label>
                        </div>
                        <div class="form-attributes @if(count($attributes) > 0) show @endif">
                            <div class="form-group">
                                <label for="selectColors">Atribute
                                    <p class="mb-0">
                                        <small>Zgjidh Atribute të tjera të këtij produkti</small>
                                    </p>
                                </label>
                                <select id="selectAttribute" multiple name="variant_attribute[]">
                                    @foreach ($variants as $variant)
                                        <option value="{{$variant->id}}">{{$variant->name}}</option>
                                    @endforeach
                                </select>
                                @error('variant_attribute') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            <div class="customer_choice_options" id="customer_choice_options">
                                @foreach ($allAttributes as $attribute)
                                    @ray(collect($attribute->options)->implode(', '))
                                    <div class="form-group row variants-option-{{$attribute->id}}">
                                        <div class="col-md-3">
                                            <input type="hidden" name="choice_no[]" value="1">
                                            <input type="text" class="form-control" name="choice[]" value="{{$attribute->name}}" placeholder="Choice Title" readonly="">
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="variants-option-{{$attribute->id}}" class="variant-input" name="variant_attributes[{{$attribute->id}}]" value="{{collect($attribute->options)->implode(', ')}}" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Çmimi dhe stoku i Produktit</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="productprice">Çmimi për Njësi</label>
                            <input type="text" name="price" class="form-control" id="productprice" placeholder="Psh: 2" value="{{ $product->price }}">
                            @error('price') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productsku">Sku</label>
                            <input type="text" name="sku" class="form-control" id="productsku" placeholder="Sku" value="{{$product->sku}}">
                            @error('sku') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productstock">Stoku</label>
                            <input type="text" name="stock" class="form-control" id="productstock" placeholder="Psh: 5" value="{{$product->stock}}">
                            @error('stock') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="price_combination @if(count($product->variants)>0) show @endif" id="price_combination">
                            <table class="table table-bordered aiz-table footable footable-8 breakpoint-xl">
                                <thead>
                                    <tr class="footable-header">
                                        <td class="text-center footable-first-visible" style="display: table-cell;">Varianti</td>
                                        <td class="text-center" style="display: table-cell;">Çmimi i Variantit</td>
                                        <td class="text-center" data-breakpoints="lg" style="display: table-cell;">SKU</td>
                                        <td class="text-center" data-breakpoints="lg" style="display: table-cell;">Stoku</td>
                                        <td class="text-center table-variant_image" data-breakpoints="lg" style="display: table-cell;">Foto</td>
                                        <td class="footable-last-visible table-actions" style="display: table-cell;"></td>
                                    </tr>
                                </thead>
                                <tbody id="price_combination_table">
                                    @foreach ($product->variants as $item)
                                        <tr class="variant" id="table-price-{{$item->name}}">
                                            <td class="footable-first-visible">
                                                <label for="" class="control-label">{{$item->name}}</label>
                                                <input type="hidden" name="variant_id[]" value="{{$item->slug}}" class="svariant_id">
                                                <input type="hidden" name="variant_name[]" value="{{$item->name}}" class="svariant_name">
                                            </td>
                                            <td style="display: table-cell;width: 160px;">
                                                <div class="input-group">
                                                    <input type="number" lang="en" name="variant_price[]" value="{{$item->price}}" min="0" step="0.01" class="form-control small variant_price svariant_price" placeholder="{{ $product->price }}">
                                                    <span class="input-group-text small">€</span>
                                                </div>
                                            </td>
                                            <td style="width: 130px;">
                                                <input type="text" name="variant_sku[]" value="" class="form-control svariant_sku">
                                            </td>
                                            <td style="width: 100px;">
                                                <input type="number" lang="en" name="variant_qty[]" value="{{$item->stock}}" min="0" step="1" class="form-control variant_qty svariant_qty" placeholder="{{$product->stock}}">
                                            </td>
                                            <td>
                                                <div class="input-group table-variant_image @if($item->image) upload @endif" data-toggle="aizuploader" data-type="image">
                                                    <label for="variant_image_{{$item->slug}}">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">Browse</div>
                                                        </div>
                                                        <div class="form-control file-amount text-truncate">Choose file</div>
                                                    </label>
                                                    <div class="view_image">
                                                        @if($item->image)
                                                            <div class="remove-image" onclick="removeVariantImage(this)"><i class="fas fa-times"></i></div>
                                                            <img src="{{asset('/photos/'.$item->image)}}">
                                                        @endif
                                                    </div>
                                                    <input type="file" class="variants_images" id="variant_image_{{$item->slug}}" hidden>
                                                    <input type="hidden" name="variant_img[]" class="selected-files svariants_image" value="{{$item->image}}">
                                                </div>
                                            </td>
                                            <td class="footable-last-visible table-actions" style="display: table-cell;">
                                                <button type="button" class="btn btn-icon btn-sm btn-danger"
                                                    onclick="delete_variant(this)"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 product-right">
                <div class="add-product-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="product-description">
                                <div class="body-area">
                                    <div class="form-group">
                                        <label for="uploadimage">Foto Produkti</label>
                                        @livewire('upload-file', [
                                            'inputName' => 'image', 'upload' => 'single', 'exis' => $product->image, 'path'=> 'products/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni imazhin kryesor', 'maxWidth'=>700, 'maxHeight'=>700, 'uid' => 1,
                                            'sizes' => array('230','70')
                                        ])
                                    </div>
                                    <div class="form-group">
                                        <label for="uploadimage">Galeri Produkti</label>
                                        @livewire('upload-file', [
                                            'inputName' => 'gallery_image', 'upload' => 'multiple', 'exis' => $product->gallery->pluck('image'), 'path'=> 'products/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni foto të tjera për këtë produkt', 'maxWidth'=>700, 'maxHeight'=>700, 'uid' => 2,
                                            'sizes' => ['550']
                                        ])
                                    </div>
                                    @livewire('products.product-categories', ['category'=> $product->category])
                                    <div class="form-group">
                                        <label for="selectBrands">Marka
                                            <p class="mb-0">
                                                <small>Zgjidh Marken e ketij produkti nëse ka</small>
                                            </p>
                                        </label>
                                        <select id="selectBrands" name="brands">
                                            <option data-placeholder="true"></option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" @if($productBrand == $brand->id) selected @endif>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('brands') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="form-group productTags">
                                        <label for="producttags">Tags
                                            <p class="mb-0">
                                                <small>Shkruaj tags per produktin</small>
                                            </p>
                                        </label>
                                        <input type="text" value="{{$tags}}" id="producttags" name="tags" />
                                        <p class="mb-0">
                                            <small>*Pas cdo tags shtyp ',' ose 'Enter'</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Të dhënat për Dërgimin</h5>
                    </div>
                    <div class="card-body">
                        <div class="shipping-vendor" x-data="{ open_vendor: {{$product->shipping ? 'true' : 'false' }} }">
                            <div class="col-12 row nospace">
                                <label class="col-10 col-from-label title-13">Përdor çmimet për Posten të vendosura nga dyqani</label>
                                <input type="hidden" name="vendor_shipping" value="0">
                                <div class="col-2 nospace">
                                    <label class="cth-switch cth-switch-success mb-0">
                                        <input value="1" type="checkbox" name="vendor_shipping" @if($product->shipping) checked @endif x-on:click="open_vendor = !open_vendor">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 row shipping-product" :class="{ 'show ': open_vendor === true }">
                                <h6>Cmimet për Posten të vendosura nga dyqani</h6>
                                <p class="small-text">Zgjidh Cmimet e vendosura nga dyqani ose vendos cmime për postën vetëm për këtë produkt</p>
                                @foreach($product->owner->shippings as $shipping)
                                <div class="col-12 row">
                                    <div class="col-8 title-15">{{$shipping->country->name}}</div>
                                    <div class="col-4">{{($shipping->cost) ? $shipping->cost.'€' : 'Falas'}}</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-12 row shipping-product" :class="{ 'show ': open_vendor === false }">
                                <h6>Vendosni Cmimet për Posten</h6>
                                <p class="small-text">Vendosni Cmimet per Posten ose zgjidh Cmimet e vendosura nga dyqani</p>
                                @foreach ($shippingCountry as $country)
                                @php
                                    $thisShipping = $product->shippings()->where('country_id', $country->id)->first();
                                    if($thisShipping){
                                        $openCountry = ($thisShipping->shipping) ? 'true' : 'false';
                                        $openPrice = ($thisShipping->free) ? 'false' : 'true';
                                        $shprice = $thisShipping->cost;
                                        $shtime = $thisShipping->shipping_time;
                                    } else {
                                        $openCountry = 'true';
                                        $openPrice = 'false';
                                        $shprice = 0;
                                        $shtime = 1;
                                    }
                                @endphp
                                <div class="col-12 row shipping-country" x-data="{ open{{$country->id}}: {{$openCountry}}, openc{{$country->id}}: {{$openPrice}} }">
                                    <div class="col-12 row">
                                        <label class="col-9 col-from-label">Dërgim në {{$country->name}}</label>
                                        <div class="col-3">
                                            <label class="cth-switch cth-switch-success mb-0">
                                                <input type="checkbox" name="shipping[{{$country->id}}][shipping]" @if($openCountry == 'true') checked @endif x-on:click="open{{$country->id}} = !open{{$country->id}}">
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 row shipping-options" :class="{ 'show ': open{{$country->id}} === true }">
                                        <input type="hidden" name="shipping[{{$country->id}}][country_id]" value="{{$country->id}}">
                                        <label class="col-9 col-from-label">Dërgim Falas</label>
                                        <div class="col-3">
                                            <label class="cth-switch cth-switch-success mb-0" x-on:click="openc{{$country->id}} = false">
                                                <input type="radio" name="shipping[{{$country->id}}][free]" value="1" @if($openPrice == 'false') checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 row shipping-options" :class="{ 'show ': open{{$country->id}} === true }">
                                        <label class="col-9 col-from-label">Dërgim me kosto</label>
                                        <div class="col-3">
                                            <label class="cth-switch cth-switch-success mb-0" x-on:click="openc{{$country->id}} = true">
                                                <input type="radio" name="shipping[{{$country->id}}][free]" value="0" @if($openPrice == 'true') checked @endif>
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="shiping-price col-12 row" :class="{ 'show ': openc{{$country->id}} === true }">
                                            <label class="col-6 col-from-label">Çmimi për {{$country->name}}</label>
                                            <div class="col-6">
                                                <div class="input-group">
                                                    <input type="number" class="form-control xsmall" min="0" value="{{$shprice}}" step=".01" name="shipping[{{$country->id}}][cost]" placeholder="Cost">
                                                    <span class="input-group-text xsmall">€</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="shiping-price col-12 row" :class="{ 'show ': openc{{$country->id}} === true }">
                                            <label class="col-6 col-from-label">Koha e arritje së produktit</label>
                                            <div class="col-6">
                                                <select id="firstitemkoha{{$country->id}}" name="shipping[{{$country->id}}][shipping_time]" class="form-control xsmall cosformheight" required>
                                                    <option value="1" @if($shtime == 1) selected @endif>12 deri në 24 orë</option>
                                                    <option value="2" @if($shtime == 2) selected @endif>12 deri në 48 orë</option>
                                                    <option value="3" @if($shtime == 3) selected @endif>1 deri 3 ditë</option>
                                                    <option value="4" @if($shtime == 4) selected @endif>2 deri 4 ditë</option>
                                                    <option value="5" @if($shtime == 5) selected @endif>3 deri 5 ditë</option>
                                                    <option value="6" @if($shtime == 6) selected @endif>5 deri 10 ditë</option>
                                                    <option value="7" @if($shtime == 7) selected @endif>7 deri 14 ditë</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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