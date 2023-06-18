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
                                    text: 'Fjalkalimi (është i mundur vetem kur regjistrohet useri për here të pare)',
                                    onAction: function () {
                                        editor.insertContent('{$userPassword}');
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
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Antarësimi',
                                    getSubmenuItems: function () {
                                    return [
                                        {
                                            type: 'menuitem',
                                            text: 'Emri i Personit',
                                            onAction: function () {
                                                editor.insertContent('{$requestName}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Roli i Personit',
                                            onAction: function () {
                                                editor.insertContent('{$requestRole}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Konfirmo Kërkesën',
                                            onAction: function () {
                                                editor.insertContent('<a href="{$requestConfirm}"><button style="cursor: pointer; background: #fcb700; color: white; width: 220px; ; height: 35px; border: 0; border-radius: 3px;">Prano kërkesën</button></a>');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Menaxho Stafin',
                                            onAction: function () {
                                                editor.insertContent('<a href="{$requestManageStaff}"><button style="cursor: pointer; background: #fcb700; color: white; width: 220px; ; height: 35px; border: 0; border-radius: 3px;">Menaxho Stafin</button></a>');
                                            }
                                        }
                                    ];
                                    }
                                },
                            ];
                            callback(items);
                        }
                    });
                    editor.ui.registry.addMenuButton('admins', {
                        text: 'Administratori',
                        fetch: function (callback) {
                            var items = [
                                {
                                    type: 'menuitem',
                                    text: 'Konfirmo Biznesin',
                                    onAction: function () {
                                        editor.insertContent('<a href="{$vendorsRequest}"><button style="cursor: pointer; background: #fcb700; color: white; width: 220px; ; height: 35px; border: 0; border-radius: 3px;">Shiko kërkesat e dyqaneve</button></a>');
                                    }
                                }
                            ];
                            callback(items);
                        }
                    });
                    editor.ui.registry.addMenuButton('orders', {
                        text: 'Porosia',
                        fetch: function (callback) {
                            var items = [
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Porosi',
                                    getSubmenuItems: function () {
                                    return [
                                        {
                                            type: 'menuitem',
                                            text: 'Id e Porosisë',
                                            onAction: function () {
                                                editor.insertContent('{$orderId}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Detajet e Porosisë',
                                            onAction: function () {
                                                editor.insertContent('{$orderDetails}');
                                            }
                                        },
                                    ];
                                    }
                                },
                                {
                                    type: 'nestedmenuitem',
                                    text: 'Porosi e një dyqani',
                                    getSubmenuItems: function () {
                                    return [
                                        {
                                            type: 'menuitem',
                                            text: 'Id e Porosisë',
                                            onAction: function () {
                                                editor.insertContent('{$orderId}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Emri i Dyqanit',
                                            onAction: function () {
                                                editor.insertContent('{$orderVendorName}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Detajet e Porosisë',
                                            onAction: function () {
                                                editor.insertContent('{$orderVDetails}');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Gjurmimi i fundit',
                                            onAction: function () {
                                                editor.insertContent('<p style="padding:5px 10px;background-color: #f1f1f1;">{$orderTrackComment}</p>');
                                            }
                                        },
                                        {
                                            type: 'menuitem',
                                            text: 'Buton: Gjurmimi i Porosisë',
                                            onAction: function () {
                                                editor.insertContent('<a href="{$orderTrackLink}"><button style="cursor: pointer; background: #fcb700; color: white; width: 220px; ; height: 35px; border: 0; border-radius: 3px;">Informacionet rreth Gjurmimit</button></a>');
                                            }
                                        },
                                    ];
                                    }
                                },
                            ];
                            callback(items);
                        }
                    });
                }
            };
            @if(in_array($email->rights, [0, 1,4,6,7]))
                let pTmce = tinymce.init(options);
            @endif
            @if(in_array($email->rights, [2,4,5,7]))
                let voptions = { ...options};
                voptions.selector = 'textarea#vemail_templates';
                let vTmce = tinymce.init(voptions);
            @endif
            @if(in_array($email->rights, [3,5,6,7]))
                let aoptions = { ...options};
                aoptions.selector = 'textarea#aemail_templates';
                let aTmce = tinymce.init(aoptions);
            @endif
        </script>
    @endpush
    @push('styles')
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Template</h4>
        <ul class="links">
            <li>
                <a href="{{route('admin.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('admin.emails.index')}}">Template</a>
            </li>
        </ul>
    </x-slot>
    <div>
        <div class="product-area mt-2">
            <form action="{{ route('admin.emails.update', $email->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="categoryname">Emri i Formës Email</label>
                    <input type="text" name="name" class="form-control" id="categoryname" placeholder="Emri" value="{{ $email->name }}" disabled>
                    @error('name') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="subject">Subjekti i Formës Email</label>
                    <input type="text" name="subject" class="form-control" id="subject" placeholder="Subjekti" value="{{ $email->subject }}">
                    @error('subject') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                @if(in_array($email->rights, [0,1,4,6,7]))
                    <div class="form-group">
                        <label for="email_templates">Forma Email për përdoruesin</label>
                        <textarea name="email_templates" id="email_templates" class="form-control" placeholder="Forma">{{ $email->email_templates }}</textarea>
                        @error('email_templates') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                @endif
                @if(in_array($email->rights, [2,4,5,7]))
                    <div class="form-group">
                        <label for="vemail_templates">Forma Email për Dyqanin</label>
                        <textarea name="vemail_templates" id="vemail_templates" class="form-control" placeholder="Forma">{{ $email->vemail_templates }}</textarea>
                        @error('vemail_templates') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                @endif
                @if(in_array($email->rights, [3,5,6,7]))
                    <div class="form-group">
                        <label for="aemail_templates">Forma Email për Administratorin</label>
                        <textarea name="aemail_templates" id="aemail_templates" class="form-control" placeholder="Forma">{{ $email->aemail_templates }}</textarea>
                        @error('aemail_templates') <span class="text-danger error">{{ $message }}</span>@enderror
                    </div>
                @endif
                <div class="col-12 pl-0">
                    <button type="submit" class="btn btn-primary ">Ruaj</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>