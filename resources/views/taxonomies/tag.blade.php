<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $tag->name)
    <div class="container taxonomy">
        @livewire('products.list-products', ['type'=>3,'pid'=> $tag->id])
    </div>
</x-app-layout>