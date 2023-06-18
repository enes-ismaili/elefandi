<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $brand->name)
    <div class="container taxonomy">
        @livewire('products.list-products', ['type'=>4,'pid'=> $brand->id])
    </div>
</x-app-layout>