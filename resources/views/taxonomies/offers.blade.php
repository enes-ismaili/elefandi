<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $offer->name)
    <div class="container taxonomy">
        @livewire('products.list-products', ['type'=>5,'pid'=> $offer->id])
    </div>
</x-app-layout>