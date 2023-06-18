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
    <form action="{{route('admin.ads.update', $ads->id)}}" method="POST" class="">
        @csrf
        <div class="row" >
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">Ndrysho Hapsirën {{ $ads->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Emri *</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Emri" value="{{ $ads->name }}">
                            @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="price">Çmimi *</label>
                            <input type="number" step=".01" name="price" class="form-control" id="price" placeholder="Çmimi" value="{{ $ads->price }}" min="0">
                            @error('price') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="special-box">
                                    <div class="heading-area">
                                        <h4 class="title">Desktop Example Image</h4>
                                        @livewire('upload-file', [
                                            'inputName' => 'dimage', 'upload' => 'single', 'exis' => (($ads->dimage) ? $ads->dimage : ''), 'path'=> 'ads/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni imazhin shembull', 'uid' => 1
                                        ])
                                        @error('dimage') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="special-box">
                                    <div class="heading-area">
                                        <h4 class="title">Mobile Example Image</h4>
                                        @livewire('upload-file', [
                                            'inputName' => 'mimage', 'upload' => 'single', 'exis' => (($ads->mimage) ? $ads->mimage : ''), 'path'=> 'ads/', 'type'=>2, 'deleteF'=>false, 
                                            'paragraphText' => 'Ngarkoni imazhin shembull', 'uid' => 2
                                        ])
                                        @error('mimage') <span class="text-danger error">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pl-0">
                        <button type="submit" class="btn btn-success">Ruaj</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-admin-layout>