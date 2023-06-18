<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
        <style>
.cth-switch input:empty~span {
    width: 140px;
    height: 26px;
}
.cth-switch input:empty~span:before {
    line-height: 26px;
    padding-left: 8px;
    width: 140px;
}
.cth-switch input:empty~span:after {
    content: "Tani";
    width: 70px;
    height: 22px;
    line-height: 26px;
    color: #000;
    font-weight: 500;
    text-transform: uppercase;
}
.cth-switch input:checked~span:after {
    margin-left: 68px;
    content: "Me Vone";
    color: #fff;
}
.cth-switch + label {
    display: inline-block;
    margin: 0 10px;
    position: relative;
    top: -8px;
}
        </style>
    @endpush
    <form action="{{route('vendor.ads.single.store', $mads->id)}}" method="POST" class="">
        @csrf
        <div class="row">
            <div class="col-lg-12">
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
                                            'inputName' => 'dimage', 'upload' => 'single', 'exis' => ((old('dimage')) ? old('dimage') : ''), 'path'=> 'ads/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni foton e reklamës', 'maxWidth'=>$maxWidth, 'maxHeight'=>$maxHeight, 'uid' => 1
                                        ])
                                        @error('dimage') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="link">Linku ku do të dërgojë reklama *</label>
                                    <input type="text" name="link" class="form-control" id="link" placeholder="Linku" value="{{ old('link') }}">
                                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="message">Komenti Juaj për Administratorët</label>
                                    <textarea name="message" id="message" class="form-control" placeholder="Komenti Juaj">{{ old('message') }}</textarea>
                                    @error('message') <span class="text-danger error">{{ $message }}</span>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="astatus">Statusi i Reklamës</label>
                                    <select name="astatus" id="astatus" disabled>
                                        <option value="0">Në Pritje</option>
                                    </select>
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