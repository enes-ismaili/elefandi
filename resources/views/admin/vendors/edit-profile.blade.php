<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Ndrysho Profilin</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Ndrysho Profilin</span>
            </li>
        </ul>
    </x-slot>
    <div x-data="{ openR: false }">
        <form action="{{ route('vendor.store.profile', ['id' => $vendor->id]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">Të dhënat e dyqanit</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="productname">Emri i Dyqanit *</label>
                                <div class="input-group mb-3">
                                    <input type="text" name="vendor" class="form-control" id="vendor" placeholder="Emri" value="{{$vendor->name}}" disabled>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span class="text-primary cpointer" x-on:click="openR = true">Ndrysho Emrin e Dyqanit</span>
                                        </span>
                                    </div>
                                </div>
                                @error('vendor') <span class="text-danger error">{{ $message }}</span>@enderror
                                @error('newdescription') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email">Email-i *</label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Email-i *" value="{{$vendor->email}}">
                                        @error('email') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="phone">Numri i telefonit *</label>
                                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Numri i telefonit *" value="{{$vendor->phone}}">
                                        @error('phone') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-6 d-none">
                                    <div class="form-group">
                                        <label for="zipcode">Kodi postal</label>
                                        <input type="text" name="zipcode" class="form-control" id="zipcode" placeholder="Kodi postal" value="{{$vendor->zipcode}}">
                                        @error('zipcode') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                @livewire('select-countries', ['selCountry'=>$vendor->country_id, 'selCity'=>$vendor->city])
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Përshkrimi i dyqanit</label>
                                        <textarea name="description" id="description"  class="form-control">{{ $vendor->description }}</textarea>
                                        @error('description') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="address">Adresa *</label>
                                        <input type="text" name="address" class="form-control" id="address" placeholder="Adresa *" value="{{$vendor->address}}">
                                        @error('address') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">Rrjetet Sociale (Opsional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="facebook">Facebook</label>
                                        <input class="form-control" type="text" name="socials[facebook]" id="facebook" value="{{$vendor->socials()->where('name', 'facebook')->first() ? $vendor->socials()->where('name', 'facebook')->first()->links : ''}}" placeholder="Facebook (p.sh: https://facebook.com/elefandi)">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="twitter">Twitter</label>
                                        <input class="form-control" type="text" name="socials[twitter]" id="twitter" value="{{$vendor->socials()->where('name', 'twitter')->first() ? $vendor->socials()->where('name', 'twitter')->first()->links : ''}}" placeholder="Twitter (p.sh: https://twitter.com/elefandi)">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="instagram">Instagram</label>
                                        <input class="form-control" type="text" name="socials[instagram]" id="instagram" value="{{$vendor->socials()->where('name', 'instagram')->first() ? $vendor->socials()->where('name', 'instagram')->first()->links : ''}}" placeholder="Instagram (p.sh: https://instagram.com/elefandi)">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="youtube">Youtube</label>
                                        <input class="form-control" type="text" name="socials[youtube]" id="youtube" value="{{$vendor->socials()->where('name', 'youtube')->first() ? $vendor->socials()->where('name', 'youtube')->first()->links : ''}}" placeholder="Youtube (p.sh: https://youtube.com/elefandi)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">Orari i Punës</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @error('time.*') <span class="text-danger error">{{ $message }}</span>@enderror
                                <div class="col-12 row workhours">
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Hënë</label>
                                            <input type="hidden" name="time[monday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[monday]" id="monday" @if(!$vendor->workhour->monday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[monday_start]" id="monday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[monday_end]" id="monday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->monday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Martë</label>
                                            <input type="hidden" name="time[tuesday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[tuesday]" id="tuesday" @if(!$vendor->workhour->tuesday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[tuesday_start]" id="tuesday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[tuesday_end]" id="tuesday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Mërrkurë</label>
                                            <input type="hidden" name="time[wednesday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[wednesday]" id="wednesday" @if(!$vendor->workhour->wednesday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[wednesday_start]" id="wednesday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[wednesday_end]" id="wednesday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->wednesday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Enjte</label>
                                            <input type="hidden" name="time[thursday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[thursday]" id="thursday" @if(!$vendor->workhour->thursday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[thursday_start]" id="thursday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" id="thursday-starth" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[thursday_end]" id="thursday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" id="wednesday-starth" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->thursday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Premte</label>
                                            <input type="hidden" name="time[friday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[friday]" id="friday" @if(!$vendor->workhour->friday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[friday_start]" id="friday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[friday_end]" id="friday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->friday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Shtunë</label>
                                            <input type="hidden" name="time[saturday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[saturday]" id="saturday" @if(!$vendor->workhour->saturday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[saturday_start]" id="saturday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[saturday_end]" id="saturday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->saturday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 row">
                                        <div class="col-12">
                                            <label>E Dielë</label>
                                            <input type="hidden" name="time[sunday]" value="1">
                                            <div class="form-group fright">
                                                <label class="cth-switch cth-switch-success">
                                                    <input value="0" type="checkbox" name="time[sunday]" id="sunday" @if(!$vendor->workhour->sunday) checked @endif>
                                                    <span></span>
                                                </label>
                                                <span>Pushim</span>
                                            </div>
                                        </div>
                                        <div class="col-12 row hcheckbox show">
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[sunday_start]" id="sunday-start" value="{{\Carbon\Carbon::parse($vendor->workhour->sunday_start)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->sunday_start)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->tuesday_start)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group dflex">
                                                    <input type="hidden" name="time[sunday_end]" id="sunday-end" value="{{\Carbon\Carbon::parse($vendor->workhour->sunday_end)->format('H:i')}}" class="form-control small input-base">
                                                    <input type="number" min="0" max="24" value="{{\Carbon\Carbon::parse($vendor->workhour->sunday_end)->format('H')}}" class="form-control small inputtimes input-hmin">
                                                    <span class="form-control small time-separator"> : </span>
                                                    <input type="number" min="0" max="59" value="{{\Carbon\Carbon::parse($vendor->workhour->sunday_end)->format('i')}}" class="form-control small inputtimes input-hmax">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">Imazhet e dyqanit</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="logo_path">Logo e Dyqanit</label>
                                @livewire('upload-file', [
                                    'inputName' => 'logo_path', 'upload' => 'single', 'exis' => $vendor->logo_path, 'path'=> 'vendor/', 'type'=>2, 'deleteF'=>false,
                                    'paragraphText' => 'Ngarkoni logon e dyqanit tuaj', 'maxWidth'=>700, 'maxHeight'=>700, 'uid' => 1
                                ])
								@error('logo_path') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="cover_path">Cover i Dyqanit</label>
                                @livewire('upload-file', [
                                    'inputName' => 'cover_path', 'upload' => 'single', 'exis' => $vendor->cover_path, 'path'=> 'cover/', 'type'=>2, 'deleteF'=>false, 
                                    'paragraphText' => 'Ngarkoni imazhin prezantues (rezulucioni adekuat: 1230x400px)', 'maxWidth'=>1250, 'maxHeight'=>400, 'uid' => 2
                                ])
								@error('cover_path') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">Kosto Transporti</h5>
                        </div>
                        <div class="card-body">
                            <div class="transporti">
                                @foreach($shippingCountry as $shipping)
                                @php
                                    $currentShipping = $vendor->shippings()->where('country_id', $shipping->id)->first();
                                    if($currentShipping){
                                        $shippTrans = $currentShipping->transport;
                                        $shippLimit = $currentShipping->limit;
                                        $shippCost = $currentShipping->cost;
                                        $shippTransTime = $currentShipping->transtime;
                                    } else {
                                        $shippTrans = 1;
                                        $shippLimit = '';
                                        $shippCost = '';
                                        $shippTransTime = 1;
                                    }
                                @endphp
                                <div class="shipping-single" id="trans-{{$shipping->id}}">
                                    <h5 class="uppercase">{{$shipping->name}}</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="trans[{{$shipping->id}}][]" id="trans-{{$shipping->id}}-1" value="1" @if($shippTrans == 1) checked @endif>
                                        <label class="form-check-label" for="trans-{{$shipping->id}}-1">Kosto transporti për secilin produkt</label>
                                    </div>
                                    <span class="transport-info">Kostoja totale e transportit për disa produkte nga dyqani juaj do të jetë shumë e të gjithë transporteve të produkteve</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="trans[{{$shipping->id}}][]" id="trans-{{$shipping->id}}-2" value="2" @if($shippTrans == 2 || $shippTrans == 4) checked @endif>
                                        <label class="form-check-label" for="trans-{{$shipping->id}}-2">Kosto transport falas mbi një shumë të caktuar</label>
                                    </div>
                                    <div class="transportShuma" style="display: none;">
                                        <input class="form-control small" type="number" name="transLimit[{{$shipping->id}}]" value="{{$shippLimit}}" placeholder="Shuma mbi te cilen transporti eshte falas" step="0.01" min="0">
                                    </div>
                                    <span class="transport-info">Mbi një shumë të caktuar mallrash të blera tek dyqani juaj kosto transportit do të jetë Falas</span>
                                    <div class="form-check disabled">
                                        <input class="form-check-input" type="checkbox" name="trans[{{$shipping->id}}][]" id="trans-{{$shipping->id}}-3" value="3" @if($shippTrans == 3 || $shippTrans == 4) checked @endif>
                                        <label class="form-check-label" for="trans-{{$shipping->id}}-3">Kosto më e madhe e transportit të produkteve</label>
                                    </div>
                                    <span class="transport-info">Kosto e transportit më e madhe nga të gjitha produkteve të dyqanit tuaj që përdoruesi ble në dyqanin tuaj</span>
                                    <div class="form-group">
                                        <label for="trans-cost-{{$shipping->id}}">Kosto për Dërgesat në {{$shipping->name}}</label>
                                        <span class="transport-info">Kosto Transporti për produktet e dyqanit për dërgesa në {{$shipping->name}}.</span>
                                        <div class="input-group mb-3">
                                            <input class="form-control small" type="number" name="transCost[{{$shipping->id}}][]" id="trans-cost-{{$shipping->id}}" value="{{$shippCost}}" placeholder="0" step="0.01" min="0">
                                            <div class="input-group-append small">
                                                <span class="input-group-text small">€</span>
                                            </div>
                                        </div>
                                        <span class="transport-info">Lër <b>0</b> për transport falas</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="trans-cost-{{$shipping->id}}">Koha e transportit për dërgim në {{$shipping->name}}</label>
                                        <span class="transport-info">Kosto Transporti për produktet e dyqanit për dërgesa në {{$shipping->name}}.</span>
                                        <select id="firstitemkoha{{$shipping->id}}" name="transTime[{{$shipping->id}}][]" class="form-control xsmall cosformheight" required>
                                            <option value="1" @if($shippTransTime == 1) selected @endif>12 deri në 24 orë</option>
                                            <option value="2" @if($shippTransTime == 2) selected @endif>12 deri në 48 orë</option>
                                            <option value="3" @if($shippTransTime == 3) selected @endif>1 deri 3 ditë</option>
                                            <option value="4" @if($shippTransTime == 4) selected @endif>2 deri 4 ditë</option>
                                            <option value="5" @if($shippTransTime == 5) selected @endif>3 deri 5 ditë</option>
                                            <option value="6" @if($shippTransTime == 6) selected @endif>5 deri 10 ditë</option>
                                            <option value="7" @if($shippTransTime == 7) selected @endif>7 deri 14 ditë</option>
                                        </select>
                                    </div>
                                </div>
                                @error('trans') <span class="text-danger error">{{ $message }}</span>@enderror
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer pl-0">
                            <button type="submit" class="btn btn-primary ">Ruaj</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" :class="{ 'show  dshow': openR === true }" x-show="openR" @click.away="openR = false">
            <div class="modal_bg" @click="openR = false"></div>
            <div class="modal-dialog modal-lg" modal-dialog-centered" role="document">
                <form action="{{ route('vendor.store.namechange', ['id' => $vendor->id]) }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Kërkesë për ndryshim emri të dyqanit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"  @click="openR = false">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @php
                            $nameRequest = $vendor->namerequest()->where([['udelete', '=', 0],['status', '=', 0]])->first();
                        @endphp
                        <div class="content-area">
                            <div class="form-group">
                                <label for="newname">Emri i ri i Dyqanit *</label>
                                <input type="hidden" name="removeRequest" value="0">
                                <input type="text" name="newname" class="form-control" id="newname" placeholder="Emri i Dyqanit *" @if($nameRequest) value="{{$nameRequest->name}}" disabled @else value="{{$vendor->name}}" @endif>
                                @error('newname') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="newdescription">Arsyeja e Ndryshimit</label>
                                <textarea name="newdescription" id="newdescription"  class="form-control" placeholder="Arsyeja sepse dëshironi të ndryshoni emrin"  @if($nameRequest) disabled>{{ $nameRequest->description }} @else >  @endif</textarea>
                                @error('newdescription') <span class="text-danger error">{{ $message }}</span>@enderror
                            </div>
                            @if($nameRequest)
                                @php
                                    $nameChangeStatus = 'në Pritje';
                                    if($nameRequest->status == 1){
                                        $nameChangeStatus = 'Aprovuar';
                                    } elseif($nameRequest->status == 2){
                                        $nameChangeStatus = 'Refuzuar';
                                    }
                                @endphp
                                <input type="hidden" name="removeRequest" value="1">
                                <p>Ju keni një kërkesë Aktive për ndryshimin e emrit të dyqanit në <b>{{ $nameRequest->name }}</b></p>
                                <p>Statusi i kërkesës tuaj është {{ $nameChangeStatus }}</p>
                            @else
                                <div class="form-group">
                                    <p>* Kjo kërkesë do ti dërgohet administratorëve për aprovim</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="openR = false">Close</button>
                        <button type="submit" class="btn btn-warning" data-dismiss="modal">@if(!$nameRequest) Ruaj @else Anullo Kërkesën @endif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>