<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Fotot Kryesore Web</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Fotot Kryesore Web</span>
            </li>
        </ul>
    </x-slot>
    <form action="{{route('admin.homesettings.slider.update')}}" method="post">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>Fotot Kryesore</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="special-box">
                            <div class="heading-area">
                                <h4 class="title">Foto e Parë Web</h4>
                                @livewire('upload-file', [
                                    'inputName' => 'wslider1', 'upload' => 'single', 'exis' =>  (($wslider1 && $wslider1->value)?$wslider1->value:''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                    'paragraphText' => 'Foto e gjatë (rezulucioni adekuat: 441x624px)', 'maxWidth'=>500, 'maxHeight'=>630, 'uid' => 1
                                ])
                                @error('wslider1') <span class="text-danger error">{{ $message }}</span>@enderror
                                <div class="form-group">
                                    <label for="link1">Linku i kësaj foto</label>
                                    <input type="text" class="form-control" name="link1" id="link1" value="{{ (($wsliderLink1 && $wsliderLink1->value)?$wsliderLink1->value:'') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="special-box">
                            <div class="heading-area">
                                <h4 class="title">Fotot Anash 1</h4>
                                @livewire('upload-file', [
                                    'inputName' => 'wslider2', 'upload' => 'single', 'exis' => (($wslider2 && $wslider2->value)?$wslider2->value:''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                    'paragraphText' => 'Fotot anash 1 (rezulucioni adekuat: 394x300)', 'maxWidth'=>400, 'maxHeight'=>300, 'uid' => 2
                                ])
                                @error('wslider2') <span class="text-danger error">{{ $message }}</span>@enderror
                                <div class="form-group">
                                    <label for="link2">Linku i kësaj foto</label>
                                    <input type="text" class="form-control" name="link2" id="link2" value="{{ (($wsliderLink2 && $wsliderLink2->value)?$wsliderLink2->value:'') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="special-box">
                            <div class="heading-area">
                                <h4 class="title">Fotot Anash 2</h4>
                                @livewire('upload-file', [
                                    'inputName' => 'wslider3', 'upload' => 'single', 'exis' => (($wslider3 && $wslider3->value)?$wslider3->value:''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                    'paragraphText' => 'Fotot anash 2 (rezulucioni adekuat: 394x300)', 'maxWidth'=>400, 'maxHeight'=>300, 'uid' => 3
                                ])
                                @error('wslider3') <span class="text-danger error">{{ $message }}</span>@enderror
                                <div class="form-group">
                                    <label for="link3">Linku i kësaj foto</label>
                                    <input type="text" class="form-control" name="link3" id="link3" value="{{ (($wsliderLink3 && $wsliderLink3->value)?$wsliderLink3->value:'') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer pl-0">
                <button type="submit" class="btn btn-primary ">Ruaj</button>
            </div>
        </div>
    </form>
</x-admin-layout>