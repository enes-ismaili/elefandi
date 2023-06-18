<x-admin-layout>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.js"></script>
        <script type="module">
            // if(document.getElementById('selectMultiMandatory')){
            //     new SlimSelect({
            //         select: '#selectMultiMandatory',
            //         placeholder: 'Zgjidhni Kategoritë',
            //         closeOnSelect: true,
            //         limit: 3,
            //         searchText: 'Nuk u gjet asnjë kategori',
            //         searchPlaceholder: 'Kërko',
            //     })
            // }
        </script>
        <script src="{{ mix('js/datatime.js') }}"></script>
    @endpush
    @push('styles')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.0/slimselect.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('css/datatime.css') }}">
    @endpush
    <form action="{{ route('admin.offers.store', $offer->id) }}" method="POST" class="search-product">
        @csrf
        @livewire('offer.create-offer', ['vendor_id' => $vendor->id, 'exis'=> true, 'current'=>$offer, 'editRole'=> 1])
    </form>
</x-admin-layout>