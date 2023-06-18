<div>
    @if($showVendor)
        <div class="form-group">
            <label for="selectAction">Zgjidh Dyqanet</label>
            @if($selectedVendor)
            <div class="selected-vendors">
                @foreach($selectedVendor as $selVend)
                    <div>{{$selVend['name']}} <span class="close" wire:click="rmVendor('{{$selVend['id']}}')">×</span></div>
                    <input type="hidden" name="vendors[]" value="{{$selVend['id']}}">
                @endforeach
            </div>
            @endif
            <input type="text" wire:model="vend" class="form-control" placeholder="Kërko Dyqanet ...">
            @if($vendors)
            <div class="search-results">
                <ul>
                    @foreach($vendors as $vendor)
                        <li wire:click="addVendor('{{$vendor->id}}')">{{ $vendor->name }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    @endif
    <div class="form-group">
        <label for="selectAction">Zgjidh Produktet</label>
        @if($selectedProduct)
        <div class="selected-vendors">
            @foreach($selectedProduct as $selProd)
                <div>{{$selProd['name']}} <span class="close" wire:click="rmProduct('{{$selProd['id']}}')">×</span></div>
                <input type="hidden" name="products[]" value="{{$selProd['id']}}">
            @endforeach
        </div>
        @endif
        <input type="text" wire:model="prod" class="form-control" placeholder="Kërko Produket ...">
        @if($products)
        <div class="search-results">
            <ul>
                @foreach($products as $product)
                    <li wire:click="addProduct('{{$product->id}}')">
                        <span>{{ $product->name }}</span>
                        <span>{{ $product->price }}€</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
