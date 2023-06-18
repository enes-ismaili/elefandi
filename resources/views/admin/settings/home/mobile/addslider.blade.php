<x-admin-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    <form action="{{route('admin.homesettings.slidermobile.add.store')}}" method="POST" class="">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Shto Slider</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    @livewire('upload-file', [
                        'inputName' => 'image', 'upload' => 'single', 'exis' =>  ((old('image')) ? old('image') :''), 'path'=> 'slider/', 'type'=>2, 'deleteF'=>false, 
                        'paragraphText' => 'Foto e Sliderit (rezulucioni adekuat: 530x400px)', 'maxWidth'=>530, 'maxHeight'=>400, 'uid' => 1
                    ])
                    @error('image') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="link">Linku i Sliderit</label>
                    <input type="text" name="link" class="form-control" id="name" placeholder="Linku i Sliderit" value="{{ old('link') }}">
                    <p>* Linku mund te jete i nje produkti, kategorie ose dyqani të elefandit</p>
                    @error('link') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="corder">Renditja e Sliderit</label>
                    <input type="number" name="corder" class="form-control" id="corder" placeholder="Renditja i Sliderit" value="{{ old('corder') }}">
                    @error('corder') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="card-footer pl-0">
                <button type="submit" class="btn btn-primary ">Ruaj</button>
            </div>
        </div>
    </form>
</x-admin-layout>