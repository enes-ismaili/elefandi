<div>
    {{-- <div class="" wire:click="openCheckout"><i class="fas fa-cart-plus"></i>Bli me një Klik</div> --}}
    <div class="modal {{ ($showCheckout)?'show':'' }}">
        @if($showCheckout)
        <div class="modal_bg" wire:click.prevent="closeModal()"></div>
        <div class="modal-dialog">
            <form class="modal-content" wire:submit.prevent="saveCheckout">
                <div class="modal-header">
                    <div class="modal-title">Bli me një Klik</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" wire:click.prevent="closeModal()"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header"><h3>Porosia Juaj</h3></div>
                        <div class="card-body">
                            <table class="table ntopb">
                                <thead>
                                    <tr>
                                        <th class="tleft">Produkte</th>
                                        <th class="tcenter">Sasia</th>
                                        <th class="tcenter">Çmimi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="tleft">{{ $product->name }}{!! ($variant)?'<br> Varianti: '.$variantName:'' !!}</td>
                                        <td class="tcenter">{{ $stock }}</td>
                                        <td class="tcenter">{{ number_format($price, 2) }}€</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="divider"></div>
                            <div class="tworows subtotal my-5">
                                <div class="left">Nëntotal</div>
                                <div class="right"><span>{{  number_format(($price * $stock), 2) }}€</span></div>
                            </div>
                            <div class="tworows subtotal my-5">
                                <div class="left">Kosto e transportit</div>
                                <div class="right"><span>{{ ($shippingCost) ? number_format($shippingCost, 2).'€' : 'Falas' }}</span></div>
                            </div>
                            <div class="tworows totals my-5">
                                <div class="left">Total</div>
                                <div class="right"><span>{{  number_format((($price * $stock) + $shippingCost * $stock), 2) }}€</span></div>
                            </div>
                            <div class="divider"></div>
                            <div class="payment-options">
                                <div class="tworows">
                                    <label class="left">Metoda e Pagesës</label>
                                    <div class="right">PARA NË DORË</div>
                                </div>
                                <small>Paguaj pasi ta pranoni porosinë</small>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">@if($isLoggedIn)<h3>Të dhënat personale</h3>@else<h3>Të dhënat personale dhe adresa e dërgimit</h3>@endif</div>
                        <div class="card-body">
                            @php
                                if($isLoggedIn && !$isCorrect){
                                    echo '<div class="row">';
                                        echo '<div class="col-12">';
                                            echo '<span class="text-danger error">Ju duhet të përfundoni plotësimin e profilit për të vazhduar me blerjen</span>';
                                            echo '<br><a href="'.route('profile.edit').'"><b style="color: #f00;margin-bottom: 15px;display: block;">Shko tek Profili</b></a>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            @endphp
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="fname">Emri *</label>
                                        <input type="text" wire:model.defer="fname" class="form-control" id="fname" placeholder="Emri" @if($isLoggedIn) disabled @endif>
                                        @error('fname') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="lname">Mbiemri *</label>
                                        <input type="text" wire:model.defer="lname" class="form-control" id="lname" placeholder="Emri" @if($isLoggedIn) disabled @endif>
                                        @error('lname') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email">Email Adresa *</label>
                                        <input type="text" wire:model.defer="email" class="form-control" id="email" placeholder="Email" @if($isLoggedIn) disabled @endif>
                                        @error('email') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="phone">Numri i telefonit *</label>
                                        <input type="text" wire:model.defer="phone" class="form-control" id="phone" placeholder="Telefoni" @if($isLoggedIn) disabled @endif>
                                        @error('phone') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="country">Zgjidh Shtetin *</label>
                                            <select name="country" class="form-control" id="country" wire:model="country" @if($isLoggedIn) disabled @endif>
                                                <option value="">Shteti</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('country') <span class="text-danger error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="city">Qyteti *</label>
                                            @if($cities && count($cities))
                                                <select name="city" class="form-control" id="city" wire:model.defer="city" @if($isLoggedIn) disabled @endif>
                                                    <option value="">Zgjidh Qytetin</option>
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" wire:model.defer="city" class="form-control" id="city" placeholder="Qyteti *" value="" @if($isLoggedIn) disabled @endif>
                                            @endif
                                            @error('city') <span class="text-danger error">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="address">Adresa *</label>
                                        <input type="text" wire:model.defer="address" class="form-control" id="address" placeholder="Adresa" @if($isLoggedIn) disabled @endif>
                                        @error('address') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-6 dnone">
                                    <div class="form-group">
                                        <label for="zipcode">Kodi Postal</label>
                                        <input type="text" wire:model.defer="zipcode" class="form-control" id="zipcode" placeholder="Kodi Postal" @if($isLoggedIn) disabled @endif>
                                        @error('zipcode') <span class="text-danger error">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($isLoggedIn)
                        <div class="card">
                            <div class="card-header">
                                <h5>Zgjidh Adresën</h5>
                            </div>
                            <div class="card-body">
                                @foreach($userAddress as $saddress)
                                    <div class="form-group mb-25">
                                        <input type="radio" name="shippingaddress" wire:model.defer="shippingaddress" id="adr{{$saddress->id}}" value="{{$saddress->id}}" @if($saddress->primary || (count($userAddress) == 1)) checked @endif required>
                                        <label for="adr{{$saddress->id}}">{{ $saddress->name.', '.$saddress->address.', '.((is_numeric($saddress->city) && $saddress->country_id < 4)?$saddress->cityF->name:$saddress->city) }}</label>
                                    </div>
                                @endforeach
                                @error('shippingaddress') <span class="text-danger error">{{ $message }}</span> @enderror
                                <div class="row">
                                    <div class="col-12">
                                        @livewire('user.change-address', ['type'=>'add', 'addressState'=>$currentCountry, 'changeState'=>false, 'cchangeState'=>false, 'needReload'=>false])
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header"><h3>Informata shtesë</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <textarea class="form-control" wire:model.defer="additionalinformation" rows="4" id="additionalinformation" placeholder="Ju lutem shkruani të gjitha informatat shtesë në lidhje me këtë porosi."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-12">
                            @if($continueCheckout && $isCorrect)
                                <button type="submit" class="btn c2 small">Përfundo Blerjen</button>
                            @else
                                <button type="submit" class="btn c5 small" disabled>Përfundo Blerjen</button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
