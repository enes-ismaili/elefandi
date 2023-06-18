<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.8.2/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector: 'textarea#page-description',
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                imagetools_cors_hosts: ['picsum.photos'],
                menubar: 'file edit view insert format tools table help',
                toolbar1: 'formatselect | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | table link pagebreak hr fullscreen preview',
                toolbar2: 'undo redo | fontsizeselect | outdent indent | forecolor backcolor | superscript subscript removeformat charmap emoticons | insertfile image media code',
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
                content_style: `
                    body { font-family:Helvetica,Arial,sans-serif; font-size:14px }
                    .row {display: flex;margin-right: -15px;margin-left: -15px;}
                    .row [class^='col-'] {position: relative;width: 100%;padding-right: 15px;padding-left: 15px;}
                    .row .col-4 {flex: 0 0 33.333333%;max-width: 33.333333%;}
                `
            });
            tinymce.activeEditor.formatter.remove('h1');
        </script>
    @endpush
    @push('styles')
        
    @endpush
    <form method="post" action="{{ route('admin.pages.update', $page->id) }}" class="card">
        @csrf
        <div class="card-header">Ndrysho Faqen</div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Emri i Faqes</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Emri" value="{{ $page->name }}">
                @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
            </div>
            <textarea id="page-description" name="description">{{ $page->description }}</textarea>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary small">Ruaj</button>
        </div>
    </form>
</x-admin-layout>