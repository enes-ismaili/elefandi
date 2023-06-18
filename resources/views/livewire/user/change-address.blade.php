<div>
    @if($addressType)
        @if($changeState)
            @foreach($addedAddress as $saddress)
            <div class="form-group">
                <input type="radio" name="addresses" id="adr{{$saddress->id}}" value="{{$saddress->id}}" checked required>
                <label for="adr{{$saddress->id}}">{{ $saddress->name.', '.$saddress->address.', '.((is_numeric($saddress->city) && $saddress->country_id < 4)?$saddress->cityF->name:$saddress->city) }}</label>
            </div>
            @endforeach
        @endif
        <div class="btn small c1" wire:click.prevent="openModal('true')">Shto Adresë</div>
        @if($showForm)
            <div class="modal show">
                <div class="modal_bg" wire:click.prevent="closeModal()"></div>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Shto Adresë</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeModal()"><span aria-hidden="true">×</span></button>
                        </div>
                        @if (session()->has('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Personi kontaktues *</label>
                                            <input type="text" wire:model.defer="addressPerson" class="form-control">
                                            @error('addressPerson') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Numri i telefonit *</label>
                                            <input type="text" wire:model.defer="addressPhone" class="form-control">
                                            @error('addressPhone') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="country">Shteti *</label>
                                            <select class="form-control" id="country" required="" wire:model="addressState" @if(!$cchangeState) disabled @endif>
                                                <option value="">Shteti</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Qyteti *</label>
                                            @if($cities && count($cities))
                                                <select name="city" class="form-control" id="city" wire:model.defer="addressCity">
                                                    <option value="0">Zgjidh Qytetin</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="city" class="form-control" id="city" placeholder="Qyteti *" wire:model.defer="addressCity">
                                            @endif
                                            @error('addressCity') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Adresa *</label>
                                            <input type="text" wire:model.defer="addressAddress" class="form-control">
                                            @error('addressAddress') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn small c3" wire:click.prevent="addAddress">Shto Adresë</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="dflex">
            <div class="pointer btn c1 small address-edit" wire:click.prevent="openModal('true')">Ndrysho Adresën</div>
            <div class="pointer btn c2 small address-trash" wire:click.prevent="openDeleteModal()"><i class="fas fa-trash-alt"></i></div>
            @if(!$isPrimary)<div class="pointer btn c3 small address-primary" wire:click.prevent="makePrimary()"><i class="icon-star"></i></div>@endif
        </div>
        @if($deleteForm)
        <div class="modal show">
            <div class="modal_bg" wire:click.prevent="closeDeleteModal()"></div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Konfirmoni fshirjen e Adresës</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeDeleteModal()"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">Ju po fshini Adresen me person kontaktues <b>{{ $addressPerson }}</b> dhe vendodhje në <i>{{$addressAddress.' ,'.$addressCityName.', '.$addressStateName}}</i></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-12">
                                <button class="btn small c4" wire:click.prevent="closeDeleteModal">Kthehu mbrapa</button>
                                <button class="btn small" wire:click.prevent="deletePost">Konfirmo Fshirjen</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($showForm)
            <div class="modal show">
                <div class="modal_bg" wire:click.prevent="closeModal()"></div>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Ndrysho Adresë</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeModal()"><span aria-hidden="true">×</span></button>
                        </div>
                        @if (session()->has('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Personi kontaktues *</label>
                                            <input type="text" wire:model.defer="addressPerson" class="form-control">
                                            @error('addressPerson') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Numri i telefonit *</label>
                                            <input type="text" wire:model.defer="addressPhone" class="form-control">
                                            @error('addressPhone') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    {{-- @livewire('user.select-countries', ['selCountry'=>$addressState, 'selCity'=>$addressCity]) --}}
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="country">Shteti *</label>
                                            <select class="form-control" id="country" required="" wire:model="addressState">
                                                <option value="">Shteti</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="form-group">
                                            <label>Qyteti *</label>
                                            @if($cities && count($cities))
                                                <select name="city" class="form-control" id="city" wire:model.defer="addressCity">
                                                    <option value="0">Zgjidh Qytetin</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="city" class="form-control" id="city" placeholder="Qyteti *" wire:model.defer="addressCity">
                                            @endif
                                            @error('addressCity') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Adresa *</label>
                                            <input type="text" wire:model.defer="addressAddress" class="form-control">
                                            @error('addressAddress') <span class="text-danger error">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn small c3" wire:click.prevent="saveAddress">Ruaj Adresën</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
