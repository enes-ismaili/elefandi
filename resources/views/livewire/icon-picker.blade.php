<div>
    @push('scripts')
        <script src="{{ asset('js/iconpicker.js') }}"></script>
        <script type="module">
            IconPicker.Init({
                jsonUrl: '{{ asset('js/iconpicker.json') }}',
            });
            IconPicker.Run('#GetIconPicker');

        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/iconpicker.css') }}" />
    @endpush
    <div class="form-group d-inline-block">
        <label for="selectAction">Zgjidh Ikonën</label>
        <div class="get-and-preview">
            <input type="hidden" id="IconInput" name="icon" autocomplete="off" spellcheck="false" value="{{ $selectedIcon }}">
            <div class="icon-preview" data-toggle="tooltip" title="" data-original-title="Preview of selected Icon">
                <i id="IconPreview" class="{{ $selectedIcon }}"></i>
            </div>
            <button type="button" id="GetIconPicker" data-iconpicker-input="input#IconInput" data-iconpicker-preview="i#IconPreview" class="">Zgjidh Ikon</button>
        </div>
    </div>
</div>
