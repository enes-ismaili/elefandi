<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Kërkesa për regjistrim Dyqani</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.vendors.requests')}}">Kërkesa për regjistrim Dyqani</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.vendors.requests.update', $vendor->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">Emri i Dyqanit</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ $vendor->name }}" disabled>
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="user">Emri i Zotëruesit</label>
                            <input type="text" name="user" class="form-control" id="user" value="{{ $vendor->owners->first_name.' '.$vendor->owners->last_name }}" disabled>
                            @error('user') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ $vendor->email }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="phone">Numri i telefonit</label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ $vendor->phone }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="address">Adresa </label>
                            <input type="text" name="address" class="form-control" id="address" value="{{ $vendor->address }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="zipcode">Kodi Postar</label>
                            <input type="text" name="zipcode" class="form-control" id="zipcode" value="{{ $vendor->zipcode }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="country_id">Shteti</label>
                            <input type="text" name="country_id" class="form-control" id="country_id" value="{{ $vendor->country->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="city">Qyteti</label>
                            <input type="text" name="city" class="form-control" id="city" value="{{ (is_numeric($vendor->city) && $vendor->country_id < 4)?$vendor->cities->name : $vendor->city }}" disabled>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description">Përshkrimi i dyqanit</label>
                            <textarea name="description" id="description" class="form-control" disabled>{{ $vendor->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="description">Informacion drejtuar Administratorit</label>
                            <textarea name="message" id="message" class="form-control" disabled>{{ $vendor->message }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="action">Veprimi Juaj për këtë kërkesë regjistrimi</label>
                            <select name="action" id="action" class="form-control">
                                <option value="1">Prano</option>
                                <option value="2">Refuzo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>