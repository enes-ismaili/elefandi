<x-admin-layout>
    @push('scripts')
        <script src="{{ mix('/js/datatables.js') }}"></script>
        <script>
            const dataTable = new DataTable.DataTable("#myTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 15,
                columns: [
                    { select: [2,3], sortable: false },
                ],
                labels: {
                    placeholder: "Kërko...",
                    perPage: "{select} produkte për faqe",
                    noRows: "Nuk u gjet asnjë rezultat",
                    info: "Po shihni {start} deri në {end} të {rows} rezultateve",
                },
                layout: {
                    top: "{search}",
                    bottom: "{select}{pager}"
                },
            })
        </script>
    @endpush
    @push('styles')
        <link  rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    @endpush
    <x-slot name="breadcrumb">
        <h4 class="heading">Reklamat</h4>
        <ul class="links">
            <li>
                <a href="{{route('vendor.home')}}">Dashboard </a>
            </li>
            <i class="fas fa-angle-double-right"></i>
            <li>
                <a href="{{route('vendor.ads.index')}}">Reklamat</a>
            </li>
        </ul>
    </x-slot>
    @foreach($ads as $ad)
    <div class="card">
        <div class="card-header">Reklamat në {{ $ad->name }}</div>
        <div class="card-body">
            <div class="ads-place">
                <div class="place-header ads-s">
                    <div class="image">Foto Shembull</div>
                    <div class="size">Madhësia</div>
                    <div class="price">Çmimi</div>
                    <div class="ads">Reklama Aktive</div>
                    <div class="ads">Reklama në Pritje</div>
                    <div class="actions">Veprime</div>
                </div>
                <div class="place-single ads-s">
                    <div class="image">
                        <img src="{{ asset('photos/ads/'.$ad->dimage) }}" alt="">
                        <img src="{{ asset('photos/ads/'.$ad->mimage) }}" alt="">
                    </div>
                    <div class="size">{{ $ad->size }}px</div>
                    <div class="price">{{ $ad->price }}€</div>
                    <div class="ads"><span class="dmobile">Aktive </span>{{ $ad->ads()->where([['vendor_id', '=', current_vendor()->id],['astatus', '=', 1]])->count() }}</div>
                    <div class="ads"><span class="dmobile">Pritje </span>{{ $ad->ads()->where([['vendor_id', '=', current_vendor()->id],['astatus', '=', 0]])->count() }}</div>
                    <div class="action-icons">
                        <a href="{{ route('vendor.ads.view', [$ad->id]) }}" class="action-icon" title="Shiko"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-admin-layout>