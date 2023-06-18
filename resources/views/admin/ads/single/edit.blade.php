<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
    @endpush
    <form action="{{route('admin.ads.single.update', [$mads->id, $ads->id])}}" method="POST" class="">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Shto Reklamë për hapsirën {{ $mads->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <div class="special-box">
                                    <div class="heading-area">
                                        <h4 class="title">Imazhi i Reklamës</h4>
                                        @php
                                            if($mads->id == 1){
                                                $maxWidth = 400;
                                                $maxHeight = 200;
                                            } else {
                                                $maxWidth = 300;
                                                $maxHeight = 250;
                                            }
                                        @endphp
                                        @livewire('upload-file', [
                                            'inputName' => 'dimage', 'upload' => 'single', 'exis' => (($ads->dimage) ? $ads->dimage : ''), 'path'=> 'ads/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni foton e reklamës', 'maxWidth'=>$maxWidth, 'maxHeight'=>$maxHeight, 'uid' => 1
                                        ])
                                        @error('dimage') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="link">Linku ku do të dërgojë reklama *</label>
                                    <input type="text" name="link" class="form-control" id="link" placeholder="Linku" value="{{ $ads->link }}">
                                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="message">Komenti Juaj për Administratorët</label>
                                    <textarea name="message" id="message" class="form-control" placeholder="Komenti Juaj">{{ $ads->message }}</textarea>
                                    @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="astatus">Statusi i Reklamës</label>
                                    <select name="astatus" id="astatus">
                                        <option value="0" @if($ads->astatus == 0) selected @endif>Në Pritje</option>
                                        <option value="1" @if($ads->astatus == 1) selected @endif>Aktive</option>
                                        <option value="2" @if($ads->astatus == 2) selected @endif>Jo Aktive</option>
                                    </select>
                                </div>
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
                        <div class="row">
                            <div class="col-12">
                                <h6>Fallsifiko Shikimet</h6>
                                <div class="form-group">
                                    <label for="fvaction">Veprimi</label>
                                    <select name="fvaction" id="fvaction">
                                        <option value="0" @if($ads->fvaction == 0) selected @endif>Lër Reale</option>
                                        <option value="1" @if($ads->fvaction == 1) selected @endif>Shto me nje numer Shikimet</option>
                                        <option value="2" @if($ads->fvaction == 2) selected @endif>Shumzo me nje numer Shikimet</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fview">Numri që do të shtohet ose shumzohen shikimet</label>
                                    <input type="text" name="fview" class="form-control" id="fview" placeholder="SHikimet Fallco" value="{{ $ads->fview * 1 }}">
                                    <p>* Kujdes Zgjidh Lër Reale tek Veprimi për të hequr fallsifikimet</p>
                                    @error('fview') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h6>Fallsifiko Klikimet</h6>
                                <div class="form-group">
                                    <label for="fcaction">Veprimi</label>
                                    <select name="fcaction" id="fcaction">
                                        <option value="0" @if($ads->fcaction == 0) selected @endif>Lër Reale</option>
                                        <option value="1" @if($ads->fcaction == 1) selected @endif>Shto me nje numer Klikimet</option>
                                        <option value="2" @if($ads->fcaction == 2) selected @endif>Shumzo me nje numer Klikimet</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fclick">Numri që do të shtohet ose shumzohen klikimet</label>
                                    <input type="text" name="fclick" class="form-control" id="fclick" placeholder="SHikimet Fallco" value="{{ $ads->fclick * 1 }}">
                                    <p>* Kujdes Zgjidh Lër Reale tek Veprimi për të hequr fallsifikimet</p>
                                    @error('fclick') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
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