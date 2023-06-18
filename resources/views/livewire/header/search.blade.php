<div class="main-search form-control">
    <div class="select-box">
        <div class="select-box__current" tabindex="1">
            <div class="select-box__value">
                <input class="select-box__input" wire:model.defer="search_categories" type="radio" id="0" value="0" name="search_categories" checked="checked" />
                <p class="select-box__input-text">Të gjitha</p>
            </div>
            @foreach($categories as $category)
                <div class="select-box__value">
                    <input class="select-box__input" wire:model.defer="search_categories" type="radio" id="{{$category->id}}" value="{{$category->id}}" name="search_categories" />
                    <p class="select-box__input-text">{{$category->name}}</p>
                </div>
            @endforeach
            <img class="select-box__icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true" />
        </div>
        <ul class="select-box__list">
            <li><label class="select-box__option" for="0" aria-hidden="aria-hidden">Të gjitha</label></li>
            @foreach($categories as $category)
                <li><label class="select-box__option" for="{{$category->id}}" aria-hidden="aria-hidden">{{$category->name}}</label></li>
            @endforeach
        </ul>
    </div>
    <input type="text" class="form-control" id="search" name="squery" placeholder="Kërko..." wire:model.debounce.200ms="searchQuery" autocomplete="off" wire:keydown.enter="searchB()">
    <button type="submit" wire:click="searchB()"><i class="fas fa-search"></i>Kërko</button>
    @if($showResults || $noResults)
    <div class="search-results">
        <ul>
            @foreach($searchs as $search)
                <li class="product">
                    @php
                        $sType = 0;
                        if(!isset($search['vendor_id'])){
                            $image = $search['logo_path'];
                            $imageLink = asset('/photos/vendor/'.$image);
                            $link = route('single.vendor', $search['slug']);
                            $pname = $search['name'];
                            if($search['verified']){
                                $pname .= '<img class="vendor-verification" title="Dyqan i verifikuar" alt="Dyqan i verifikuar" src="'.asset('/images/verified.png').'">';
                            }
                            $sType = 1;
                        } else {
                            $image = $search['image'];
                            $imageLink = ((\File::exists('photos/products/70/'.$image)) ? asset('/photos/products/70/'.$image) :  asset('/photos/products/'.$image));
                            $link = route('single.product', [$search['owner']['slug'], $search['id']]);
                            $pname = $search['name'];
                        }
                    @endphp
                    <a href="{{ $link }}">
                        <img src="{{ $imageLink }}" alt="">
                        <div class="name {{ ($sType)?'v':'' }}">{!! $pname !!}</div>
                    </a>
                </li>
            @endforeach
            @if($showMore)
                <li class="product">
                    <span class="more-results pointer" wire:click="searchB()">Shiko më shumë...</span>
                </li>
            @endif
            @if($noResults)
                <li class="product">
                    <span class="no-results">Nuk ka rezultate për këtë kërkim</span>
                </li>
            @endif
        </ul>
    </div>
    @endif
</div>
