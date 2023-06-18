<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', 'Lista e dëshirave')
    <div class="container">
        <h1 class="tcenter">Lista e dëshirave</h1>
        <p class="page-information tcenter">Lista me produktet që keni ruajtur në listën e dëshirave</p>
        @livewire('products.fullwishlist')
    </div>
    
</x-app-layout>