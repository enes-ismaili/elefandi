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
.hide-vend {
    overflow: hidden;
    max-height: 0;
    transition: all .2s ease;
}
.administrator .hide-vend {
    max-height: 999px;
    transition: all .2s ease;
}
.hide-admin {
    overflow: hidden;
    transition: all .2s ease;
    max-height: 999px;
}
.administrator .hide-admin {
    max-height: 0;
    transition: all .2s ease;
}
        </style>
    @endpush
    <form action="{{route('admin.roles.add.submit')}}" method="POST" class="">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Shto Rol</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Emri i Rolit *</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Emri" value="{{ old('name') }}">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="productdesc">Përshkrimi i Rolit</label>
                            <textarea class="form-control" name="description" id="productdesc" rows="3" placeholder="Përshkrimi">{{ old('description') }}</textarea>
                            @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="selectAction">Zgjidh për kë është ky rol *</label>
                            <select id="selectAction" name="action" value="{{ old('action') }}">
                                <option value="0">Dyqanet</option>
                                <option value="1">Administratorët</option>
                            </select>
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
                        <h6>Zgjidh të drejtat e këtij roli</h6>
                        <p class="small text-warning">Të drejtat e këtij përdoruesi nuk mund të ndryshohet</p>
                        <div class="row hide-vend">
                            <label class="col-10 col-from-label title-13">Menaxhon Përdoruesit</label>
                            <input type="hidden" name="manage_users" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_users" {{ (old('manage_users')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row hide-vend">
                            <label class="col-10 col-from-label title-13">Menaxhon Dyqanet</label>
                            <input type="hidden" name="manage_vendors" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_vendors" {{ (old('manage_vendors')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row hide-vend mb-3">
                            <label class="col-10 col-from-label title-13">Menaxhon Faqet, Rolet</label>
                            <input type="hidden" name="manage_pages" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_pages" {{ (old('manage_pages')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row hide-admin">
                            <label class="col-10 col-from-label title-13">Menaxhon Dyqanin</label>
                            <input type="hidden" name="manage_vendor" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_vendor" {{ (old('manage_vendor')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Story</label>
                            <input type="hidden" name="manage_stories" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_stories" {{ (old('manage_stories')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Reklamat</label>
                            <input type="hidden" name="manage_ads" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_ads" {{ (old('manage_ads')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Lajmërimet</label>
                            <input type="hidden" name="manage_notifications" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_notifications" {{ (old('manage_notifications')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div> --}}
                        <div class="row hide-admin">
                            <label class="col-10 col-from-label title-13">Menaxhon Chatin</label>
                            <input type="hidden" name="manage_chat" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_chat" {{ (old('manage_chat')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Suportin</label>
                            <input type="hidden" name="manage_supports" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_supports" {{ (old('manage_supports')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Ofertat & Kuponat</label>
                            <input type="hidden" name="manage_offers" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_offers" {{ (old('manage_offers')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Produktet</label>
                            <input type="hidden" name="manage_products" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_products" {{ (old('manage_products')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Menaxhon Porositë</label>
                            <input type="hidden" name="manage_orders" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="manage_orders" {{ (old('manage_orders')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-10 col-from-label title-13">Të Drejta Fshirje</label>
                            <input type="hidden" name="delete_rights" value="0">
                            <div class="col-2 nospace">
                                <label class="cth-switch cth-switch-success mb-0">
                                    <input value="1" type="checkbox" name="delete_rights" {{ (old('delete_rights')) ? 'checked' : '' }}>
                                    <span></span>
                                </label>
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
<script>
    let roleType = document.querySelector('#selectAction');
    if(roleType){
        roleType.addEventListener('change', e => {
            checkType(e.target.value);
        })
    }
    function checkType(type){
        let form = document.querySelector('form');
        if(type == 1){
            form.classList.add('administrator');
        } else {
            if(form.classList.contains('administrator')){
                form.classList.remove('administrator');
            }
        }
    }
</script>
</x-admin-layout>