<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        if (Cache::has('pcat')) {
            $allCategories = Cache::get('pcat');
        } else {
            $allCategories = Cache::rememberForever('pcat', function () {
                return App\Models\Category::where('parent', '0')->get();
            });
        }
        if (Cache::has('settings')) {
            $allSettings = Cache::get('asettings');
        } else {
            $allSettings = Cache::rememberForever('asettings', function () {
                return App\Models\Setting::all()->pluck('value', 'name');
            });
        }
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if($allSettings['favicon'])<link rel="icon" href="{{ asset('photos/images/'.$allSettings['favicon']) }}" type="image/png" sizes="32x32">@endif
        @if(Request::routeIs('home'))
            <title>{{ (isset($pageTitle)) ? 'Elefandi' : $allSettings['pagetitlehome'] }}</title>
        @else
            <title>@yield('pageTitle') | {{ $allSettings['pagetitle'] }}</title>
        @endif
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        @stack('styles')
        <link rel="stylesheet" href="{{ mix('css/slider.css') }}">
        <link rel="stylesheet" href="{{ mix('css/afont.css') }}">
        <link rel="stylesheet" href="{{ mix('css/style.css') }}">
        @livewireStyles
        @php
            if(current_user()){
                $cuuid =  current_user()->uuid;
                if(current_user() && current_vendor()) {
                    $cuvid = current_vendor()->uvid;
                }
            } else {
                $cuuid = (isset($_COOKIE['uuids']) && $_COOKIE['uuids']) ? $_COOKIE['uuids'] : '';
            }
        @endphp
        <script>
            window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';
            @if(current_user())
                window.cuidd='{{ current_user()->id }}';
                @if(current_vendor())
                    window.vuidd='{{ current_vendor()->id }}';
                @endif
            @else
                window.cuidd = '{{ (isset($_COOKIE['uuids']) && $_COOKIE['uuids']) ? $_COOKIE['uuids'] : 'testttt' }}';
            @endif
        </script>
        <script src="https://{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT')}}/socket.io/socket.io.js"></script>
    </head>
    <body class="font-sans antialiased">
        @include('layouts.header')

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        @stack('modals')
        @include('admin.flash.notifications')
        @include('layouts.footer')
        <script type="module">
            import Swiper from '{{ asset('js/swiper.min.js') }}';
            // window.swiper = new Swiper;
            let swipers = {};
            window.initSwiper = (element, options = {}) => {
                if(swipers['s'+element]){
                    swipers['s'+element].destroy();
                }
                let swiper = new Swiper('.'+element, options)
                // swipers.push(swiper);
                swipers['s'+element] = swiper
            }
        </script>
        <script src="{{ mix('js/app.js') }}" defer></script>
        @livewireScripts
        {{-- <script src="{{ asset('js/swiper.js') }}" defer></script> --}}
        <script src="{{ mix('js/laravel-echo-setup.js') }}"></script>
        @stack('scripts')
        <script type="text/javascript">
            @if(current_user() && current_vendor() && check_permissions('manage_chat'))
                window.Echo.channel('chat-list').listen('.MessageReceive-u{{$cuuid}}', (data) => {
                    console.log(data);
                    window.livewire.emitTo('header.mini-chat', 'recive-message', data.message.id, data.message.chat_id);
                });
                window.Echo.channel('chat-list').listen('.MessageReceive-v{{$cuvid}}', (data) => {
                    window.livewire.emitTo('header.mini-chat', 'recive-message', data.message.id, data.message.chat_id);
                });
            @else
                window.Echo.channel('chat-list').listen('.MessageReceive-u{{$cuuid}}', (data) => {
                    console.log(data);
                    window.livewire.emitTo('header.mini-chat', 'recive-message', data.message.id, data.message.chat_id);
                });
            @endif
        </script>
    </body>
</html>