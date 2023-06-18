<x-admin-layout>
    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.8.2/tinymce.min.js"></script>
    <script>
        let options = {
            selector: 'textarea#email_templates',
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar1: 'formatselect | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | table link pagebreak hr fullscreen preview',
            toolbar2: 'undo redo | fontsizeselect | outdent indent | forecolor backcolor | superscript subscript removeformat charmap emoticons | insertfile image media code | users vendors admins orders datas',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            autosave_prefix: '{path}{query}-{id}-',
            autosave_restore_when_empty: false,
            autosave_retention: '2m',
            image_advtab: true,
            link_list: [
                { title: 'My page 1', value: 'https://www.tiny.cloud' },
                { title: 'My page 2', value: 'http://www.moxiecode.com' }
            ],
            image_list: [
                { title: 'My page 1', value: 'https://www.tiny.cloud' },
                { title: 'My page 2', value: 'http://www.moxiecode.com' }
            ],
            image_class_list: [
                { title: 'None', value: '' },
                { title: 'Some class', value: 'class-name' }
            ],
            importcss_append: true,
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'file') {
                callback('https://www.google.com/logos/google.jpg', { text: 'My text' });
                }
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', { alt: 'My alt text' });
                }
                if (meta.filetype === 'media') {
                    callback('movie.mp4', { source2: 'alt.ogg', poster: 'https://www.google.com/logos/google.jpg' });
                }
            },
            fontsize_formats: '8px 10px 12px 14px 16px 18px 20px 24px 36px 48px',
            block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre',
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            quickbars_insert_toolbar: false,
            noneditable_noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'copy cut paste removeformat link lists image imagetools table',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function (editor) {
                var toggleState = false;
                editor.ui.registry.addMenuButton('users', {
                    text: 'Përdoruesit',
                    fetch: function (callback) {
                        var items = [
                            {
                                type: 'menuitem',
                                text: 'Emri',
                                onAction: function () {
                                    editor.insertContent('{$userFName}');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Mbiemri',
                                onAction: function () {
                                    editor.insertContent('{$userLName}');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Emaili',
                                onAction: function () {
                                    editor.insertContent('{$userEmail}');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Konfirmo Emailin',
                                onAction: function () {
                                    editor.insertContent('<a href="{$userConfirmEmail}"><button style="cursor: pointer; background: #fcb700; color: white; width: 220px; ; height: 35px; border: 0; border-radius: 3px;">Konfirmo Email Adresën</button></a>');
                                }
                            },
                        ];
                        callback(items);
                    }
                });
                editor.ui.registry.addMenuButton('vendors', {
                    text: 'Dyqani',
                    fetch: function (callback) {
                        var items = [
                            {
                                type: 'menuitem',
                                text: 'Emri',
                                onAction: function () {
                                    editor.insertContent('{$vendorName}');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Logo',
                                onAction: function () {
                                    editor.insertContent('{$vendorLogo}');
                                }
                            },
                        ];
                        callback(items);
                    }
                });
            }
        };
        let pTmce = tinymce.init(options);
    </script>
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Emailet</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.emails.index')}}">Emailet</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.emails.sendpost') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="type">Lloji i marrësit</label>
                    <select name="type" id="type">
                        <option value="1">Përdorues</option>
                        <option value="2">Dyqan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="actions">Marrësi nga shteti</label>
                    <select name="country" id="country">
                        <option value="0">Të gjithë</option>
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="categoryname">Subjekti i Emailit</label>
                    <input type="text" name="subject" class="form-control" id="categoryname" placeholder="Subjekti" value="{{ old('subject') }}">
                    @error('subject') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <p class="text-warning">Kujdes: Mos përdorni të dhenat e dyqanit në përdorues ose anasjelltas</p>
                <div class="form-group">
                    <label for="email_templates">Forma Email</label>
                    <textarea name="email_templates" id="email_templates" class="form-control" placeholder="Forma">{{ old('email_templates') }}</textarea>
                    @error('email_templates') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>