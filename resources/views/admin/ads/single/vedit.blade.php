<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
    @endpush
    <form action="{{route('vendor.ads.single.update', [$mads->id, $ads->id])}}" method="POST" class="">
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
                                    <select name="astatus" id="astatus" disabled>
                                        @if($ads->astatus == 0)<option value="0">Në Pritje</option>@endif
                                        @if($ads->astatus == 1)<option value="1">Aktive</option>@endif
                                        @if($ads->astatus == 2)<option value="2">Jo Aktive</option>@endif
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