<x-app-layout>
    @push('scripts')
    @endpush
    @push('styles')
    @endpush
    @section('pageTitle', $newquery[0])
    <div class="container taxonomy">
        @livewire('products.list-products', ['type'=>1, 'pid'=> $newquery[0], 'cat'=> request()->get('cat')])
    </div>
</x-app-layout>