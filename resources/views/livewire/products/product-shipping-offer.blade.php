<div>
    @if($hasShippingS)
    <div class="product-owner-offer">
        Për çdo blerje mbi <b>{{ $shippingLimit }}€</b> në dyqanin <span>{{ $vendor->name }}</span> përfitoni transportin <b>FALAS për {{ $countryName->name }}</b>
    </div>
    @endif
</div>