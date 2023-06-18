<div class="col-12">
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="country">Zgjidh Shtetin *</label>
                <select name="country" class="form-control" id="country" wire:model="selCountry" @if($disabled) disabled @endif>
                    <option value="">Shteti</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @if($country->id == 4) disabled @endif>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label for="city">Qyteti *</label>
                @if($cities && count($cities))
                    <select name="city" class="form-control" id="city" wire:model.defer="selCity" @if($disabled) disabled @endif>
                        <option value="0">Zgjidh Qytetin</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" name="city" class="form-control" id="city" placeholder="Qyteti *" value="{{ $selCity }}" @if($disabled) disabled @endif>
                @endif
            </div>
        </div>
    </div>
</div>
