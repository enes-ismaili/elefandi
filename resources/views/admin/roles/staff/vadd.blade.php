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
.role-description .roles {
    overflow: hidden;
    max-height: 0;
    transition: all .2s ease;
}
.role-description .roles.active {
    max-height: 999px;
    transition: all .2s ease;
}
        </style>
    @endpush
    <form action="{{route('vendor.staff.add.submit')}}" method="POST" class="">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Shto Staf</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="user">Shkruaj Emailin e përdoruesit</label>
                            <input type="text" name="user_email" class="form-control" placeholder="Emaili i Përdoruesit">
                            @error('user_email') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="selectAction">Zgjidh rolin *</label>
                            <select id="selectAction" name="action" value="{{ old('action') }}">
                                @foreach($roles as $role)
                                    @if($role->id != 2)
                                    <option value="{{ $role->id }}" {{ (old('action') == $role->id) ? ' selected' : ''}}>{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('action') <span class="text-danger error">{{ $message }}</span>@enderror
                            <div class="form-group role-description">
                                @foreach($roles as $role)
                                    @if($role->id != 2)
                                    <div class="roles role{{ $role->id }}">{{ ($role->description) ? $role->description : 'Ska Përshkrim për këtë rol' }}</div>
                                    @endif
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
<script>
    let roleType = document.querySelector('#selectAction');
    if(roleType){
        checkType({{ (old('action')) ? old('action') : 1 }});
        roleType.addEventListener('change', e => {
            checkType(e.target.value);
        })
    }
    function checkType(type){
        // let form = document.querySelector('form');
        // form.classList = '';
        // form.classList.add('role'+type);
        if(type){
            let roleDesc = document.querySelector('.role-description');
            let currentDesc = roleDesc.querySelector('.active');
            let newDesc = roleDesc.querySelector('.role'+type);
            if(currentDesc){
                currentDesc.classList.remove('active');
            }
            newDesc.classList.add('active');
        }
    }
</script>
</x-admin-layout>