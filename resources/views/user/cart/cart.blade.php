<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', 'Shporta e blerjes')
    <div class="container">
        <div class="cart-page">
            <div class="page-title">Shporta e blerjes</div>
            <div class="cart-main">
                <div class="table-responsive">
                    @livewire('cart.view-cart')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>