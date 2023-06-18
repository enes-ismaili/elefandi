<div>
    @push('scripts')
    <script type="module">
        import Swiper from '{{ asset('js/swiper.min.js') }}';
        window['watchedProductsSlider'] = new Swiper(".watchedSwiper", {
            slidesPerView: 4,
            spaceBetween: 10,
        });
    </script>
    @endpush
    <div class="viewed_products_button{{ ($openWatched)?' open':'' }}" wire:click="openW()">Produktet e shikuara së fundmi<i class="fas fa-chevron-down"></i></div>
    <div class="watched-products{{ ($openWatched)?' open':'' }}">
        <div class="bg_modal" wire:click.prevent="closeW"></div>
        <div class="swiper-container watchedSwiper">
            <div class="swiper-wrapper">
                @if($openWatched)
                @forelse ($products as $product)
                @php
                    $currProduct = App\Models\Product::where('id', '=', $product)->first();
                @endphp
                @if($currProduct)
                <div class="watched-product swiper-slide">
                    <a href="{{ route('single.product', [$currProduct->owner->slug, $currProduct->id]) }}">
                        <div class="thumbnail">
                            @if(file_exists(public_path('/photos/products/230/'.$currProduct->image)))
                                <img src="{{ asset('/photos/products/230/'.$currProduct->image) }}" class="swiper-lazy" />
                            @else
                                <img src="{{ asset('/photos/products/'.$currProduct->image) }}" class="swiper-lazy" />
                            @endif
                        </div>
                        <div class="name">{{ $currProduct->name }}</div>
                    </a>
                </div>
                @endif
                @empty
                    <div class="tcenter">Ju nuk keni shikuar ende asnjë produkt! </div>
                @endforelse
                @endif
            </div>
        </div>
    </div>
</div>