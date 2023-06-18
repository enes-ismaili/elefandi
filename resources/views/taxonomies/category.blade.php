<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $category->name)
    <div class="container taxonomy">
        @livewire('products.list-products', ['type'=>2,'pid'=> $category->id])
    </div>
</x-app-layout>