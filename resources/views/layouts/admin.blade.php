<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
        if (Cache::has('settings')) {
            $allSettings = Cache::get('asettings');
        } else {
            $allSettings = Cache::rememberForever('asettings', function () {
                return App\Models\Setting::all()->pluck('value', 'name');
            });
        }
        @endphp
        @if($allSettings['favicon'])<link rel="icon" href="{{ asset('photos/images/'.$allSettings['favicon']) }}" type="image/png" sizes="32x32">@endif
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="{{ mix('css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ mix('css/adcustom.css') }}">
        <link rel="stylesheet" href="{{ mix('css/adstyle.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="{{ mix('css/afont.css') }}">
        @stack('styles')
        @livewireStyles
        @php
            if(current_vendor()){
                $cuvid = current_vendor()->uvid;
            }
        @endphp
        <script>
            window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';
        </script>
        <script src="https://{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT')}}/socket.io/socket.io.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="page">
            <div class="page-main">
                @include('admin.base.header')
                <main class="wrapper">
                    @if(Request::is('admin') || Request::is('admin/*'))
                        @include('admin.base.sidebar')
                    @else
                        @include('admin.base.vsidebar')
                    @endif
                    <div class="content-area">
                        @if (isset($breadcrumb))
                            <div class="mr-breadcrumb">
                                <div class="row">
                                    <div class="col-lg-12">
                                        {{ $breadcrumb }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
        
                @stack('modals')
                @include('admin.flash.notifications')
                <script>
                    
                </script>
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ju po Fshini një <span class="modalType"></span></h5>
                                <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="modalText"></div>
                                Jeni të sigurtë që doni ta fshini këtë <span class="modalType"></span>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary closeModal">Anullo</button>
                                <a href="" class="btn btn-danger deleteLink">Fshi</a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-backdrop fade"></div>
                </div>
                <script></script>
                <script src="{{ mix('js/admin.js') }}" defer></script>
                @livewireScripts
                {{-- <script src="{{ mix('js/app.js') }}"></script> --}}
                {{-- <script>window.Alpine.start();</script> --}}
                <script src="{{ mix('js/laravel-echo-setup.js') }}"></script>
                @stack('scripts')
                @if(current_vendor())
                <script type="text/javascript">
                    window.Echo.channel('chat-list')
                    .listen('.MessageReceive-v{{$cuvid}}', (data) => {
                        console.log(data)
                        window.livewire.emitTo('chat.vendor-chat', 'recive-message', data.message.id, data.message.chat_id);
                    });
                </script>
                @endif
            </div>
        </div>
    </body>
</html>
