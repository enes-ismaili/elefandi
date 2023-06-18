<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Rregullimet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <span>Rregullimet</span>
            </li>
        </ul>
    </x-slot>
    <form action="{{route('admin.settings.save')}}" method="post">
    <div class="card">
        @csrf
        <div class="card-header"><h5>Logo</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="special-box">
                        <div class="heading-area">
                            <h4 class="title">Header Logo</h4>
                            @livewire('upload-file', [
                                'inputName' => 'logo', 'upload' => 'single', 'exis' =>  (($logo) ? $logo->value : ''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                'paragraphText' => 'Ngarkoni Logon në Header', 'maxWidth'=>260, 'maxHeight'=>60, 'uid' => 1
                            ])
                            @error('logo') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="special-box">
                        <div class="heading-area">
                            <h4 class="title">Footer Logo</h4>
                            @livewire('upload-file', [
                                'inputName' => 'footer', 'upload' => 'single', 'exis' => (($footer_logo) ? $footer_logo->value : ''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                'paragraphText' => 'Ngarkoni Logon në Footer', 'maxWidth'=>300, 'maxHeight'=>100, 'uid' => 2
                            ])
                            @error('footer') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="special-box">
                        <div class="heading-area">
                            <h4 class="title">Invoice Logo</h4>
                            @livewire('upload-file', [
                                'inputName' => 'invoice', 'upload' => 'single', 'exis' => (($invoice_logo) ? $invoice_logo->value : ''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                'paragraphText' => 'Ngarkoni Logon për fatura', 'maxWidth'=>300, 'maxHeight'=>100, 'uid' => 3
                            ])
                            @error('invoice') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="special-box">
                        <div class="heading-area">
                            <h4 class="title">Favicon</h4>
                            @livewire('upload-file', [
                                'inputName' => 'favicon', 'upload' => 'single', 'exis' => (($favicon) ? $favicon->value : ''), 'path'=> 'images/', 'type'=>2, 'deleteF'=>false, 
                                'paragraphText' => 'Ngarkoni favicon', 'maxWidth'=>100, 'maxHeight'=>100, 'uid' => 4
                            ])
                            @error('favicon') <span class="text-danger error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header"><h5>Rregullimet e faqes</h5></div>
        <div class="card-body">
            <div class="crow">
                <div class="col-12">
                    <div class="form-group">
                        <label for="pagetitlehome">Titulli i Faqes Homepage</label>
                        <input type="text" name="pagetitlehome" class="form-control" id="pagetitlehome" placeholder="Titulli i Faqes ne Homepage" value="{{ ($pagetitlehome)? $pagetitlehome->value :'' }}">
                        @error('pagetitlehome') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="pagetitle">Prapashtesa e Titullit të Faqes</label>
                        <input type="text" name="pagetitle" class="form-control" id="pagetitle" placeholder="Titulli i Faqes" value="{{ ($pagetitle)? $pagetitle->value :'' }}">
                        <p class="small">* Titulli i faqes ne faqet e tjera. Psh: "Produkti X | Elefandi"</p>
                        @error('pagetitle') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header"><h5>Rregullimet e Footer</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="footertel">Telefononi ne</label>
                        <input type="text" name="footertel" class="form-control" id="footertel" placeholder="Nr Telefoni" value="{{ ($footertel)? $footertel->value :'' }}">
                        @error('footertel') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="footeraddress">Adresa</label>
                        <textarea class="form-control" name="footeraddress" id="footeraddress" rows="3" placeholder="Adresa">{!! ($footeraddress)? $footeraddress->value :'' !!}</textarea>
                        @error('footeraddress') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="footertel">Email</label>
                        <input type="text" name="footermail" class="form-control" id="footermail" placeholder="Email" value="{{ ($footermail)? $footermail->value :'' }}">
                        @error('footermail') <span class="text-danger error">{{ $message }}</span>@enderror
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