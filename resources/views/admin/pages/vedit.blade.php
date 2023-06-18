<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.8.2/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector: 'textarea#page-perdorimi',
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                imagetools_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar1: 'formatselect | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | table link pagebreak hr fullscreen preview',
                toolbar2: 'undo redo | fontsizeselect | outdent indent | forecolor backcolor | superscript subscript removeformat charmap emoticons | insertfile image media',
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
                height: 300,
                image_caption: true,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                quickbars_insert_toolbar: false,
                noneditable_noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'copy cut paste removeformat link lists image imagetools table',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });
            tinymce.init({
                selector: 'textarea#page-kthimi',
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                imagetools_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar1: 'formatselect | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright alignjustify | table link pagebreak hr fullscreen preview',
                toolbar2: 'undo redo | fontsizeselect | outdent indent | forecolor backcolor | superscript subscript removeformat charmap emoticons | insertfile image media',
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
                height: 300,
                image_caption: true,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                quickbars_insert_toolbar: false,
                noneditable_noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'copy cut paste removeformat link lists image imagetools table',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
            });
        </script>
    @endpush
    @push('styles')
        
    @endpush
    <form method="post" action="{{ route('vendor.pages.update') }}" class="card">
        @csrf
        <div class="card-header">Ndrysho Faqen</div>
        <div class="card-body">
            <div class="form-group">
                <label for="page-perdorimi">Kushtet e përdorimit</label>
                <textarea id="page-perdorimi" name="perdorimi">{{ ($new) ? old('perdorimi') : $pages->perdorimi }}</textarea>
                @error('perdorimi') <span class="text-danger error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group mt-5">
                <label for="page-perdorimi">Kushtet e kthimit</label>
                <textarea id="page-kthimi" name="kthimi">{{ ($new) ? old('kthimi') : $pages->kthimi }}</textarea>
                @error('kthimi') <span class="text-danger error">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary small">Ruaj</button>
        </div>
    </form>
</x-admin-layout>