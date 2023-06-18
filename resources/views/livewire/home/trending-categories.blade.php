<div>
    <div class="category-list swiper-container categoryTSwiper">
        <div class="swiper-wrapper">
            @foreach($trendingCategories as $category)
                <div class="category swiper-slide {{ ($selectedCategory->id == $category->id) ? 'active swiper-slide-active' : '' }}">
                    <div wire:click.prevent="changeSelected({{ $category->id }}, {{ $loop->index }})">
                        <div class="image">
                            @if($category->icon)
                                <i class="{{ $category->icon }} categories__icon"></i>
                            @else
                                <img src="{{ ($category->image)?asset('photos/taxonomy/'.$category->image):'' }}" alt="" class="categories__icon">
                            @endif
                        </div>
                        <div class="category-title">{{ $category->name }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cpagination">
            <div class="swipers-button-prev"><i class="fas fa-chevron-left"></i></div>
            <div class="swipers-button-next"><i class="fas fa-chevron-right"></i></div>
        </div>
    </div>
    <div class="divider"></div>
    <div class="tag-list">
        @foreach($selectedCategory->trendingtag as $tag)
            <div class="category">
                <div class="image">
                    <img src="" alt="">
                </div>
                <a href="{{ route('tag.single', $tag->slug) }}"><div class="category-title">#{{ $tag->name }}</div></a>
                
            </div>
        @endforeach
    </div>
</div>
