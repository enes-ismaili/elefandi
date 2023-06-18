<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
        <link rel="stylesheet" href="{{ mix('css/user.css') }}">
    @endpush
    <div class="container">
        <div class="site-main profile">
            <aside class="profile-sidebar">
                @include('user.sidebar')
            </aside>
            <main class="main-content sh b1 p3">
    
            </main>
        </div>
    </div>
</x-app-layout>