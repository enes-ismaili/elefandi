<?php

namespace App\Http\Livewire\Home;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class CategoriesProduct extends Component
{
    public $category;
    public $categoryChild;
    public $currentCategory;
    public $currentCategoryId;
    public $suggestions;
    public $sliderss;
    public $productList;
    public $orderList = 1;
    public $baseCategoryIds;
    protected $categoryIds;
    public $suggestionsPost = 4;

    public $readyToLoad = false;

    public function loadPosts()
    {
        $this->readyToLoad = true;
        $this->mount();
    }

    public function mount()
    {
        $this->categoryChild = $this->category->categoryChildHome;
        $this->currentCategory = $this->category;
        $catId = $this->category->id;
        $this->currentCategoryId = $catId;
        // $this->suggestions = $this->category->products()->take(6)->get();
        $this->sliderss = $this->category->sliders;
        $this->suggestionsPost = 4;
        if($this->sliderss->count()){
            $this->suggestionsPost = 7;
        }
        // $this->productList = $this->category->allProducts()->take(4)->get();
        if (Cache::has('cat'.$catId)) {
            $baseCategoryIds = Cache::get('cat'.$catId);
        } else {
            $baseCategoryIds = Cache::rememberForever('cat'.$catId, function () {
                $data = Category::with(['products', 'childrenRecursive'])->where('id', $this->category->id)->get()->toArray();
                return Arr::pluck($this->flatten($data), 'id');
            });
        }
        $this->baseCategoryIds = $baseCategoryIds;
        $this->categoryIds = $baseCategoryIds;
        $this->productList = Product::whereIn('category_id', $baseCategoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
            ->where('status', '=', 1)->orderBy('updated_at', 'DESC')->take(4)->get();
        $inludedPost = $this->productList->pluck('id');
        $this->suggestions = Product::whereIn('category_id', $baseCategoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
            ->whereNotIn('id', $inludedPost)->where('status', '=', 1)->orderBy('updated_at', 'DESC')->take($this->suggestionsPost)->get();
    }

    public function flatten($array)
    {
        $flatArray = [];

        if (!is_array($array)) {
            $array = (array)$array;
        }

        foreach($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $flatArray = array_merge($flatArray, $this->flatten($value));
            } else {
                $flatArray[0][$key] = $value;
            }
        }

        return $flatArray;
    }

    public function render()
    {
        return view('livewire.home.categories-product');
    }

    public function selectCategory($cid)
    {
        $this->currentCategory = $this->categoryChild->where('id', $cid)->first();
        if(!$this->currentCategory){
            $this->currentCategory = $this->category;
        }
        $data = Category::with(['products', 'childrenRecursive'])->where('id', $this->currentCategory->id)->get()->toArray();
        $categoryIds = Arr::pluck($this->flatten($data), 'id');
        $this->categoryIds = $categoryIds;
        // $this->suggestions = $this->currentCategory->products()->take(6)->get();
        // $this->productList = $this->currentCategory->products()->take(4)->get();

        $this->productList = Product::whereIn('category_id', $categoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->orderBy('updated_at', 'DESC')->take(4)->get();
        $inludedPost = $this->productList->pluck('id');
        $this->suggestions = Product::whereIn('category_id', $categoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereNotIn('id', $inludedPost)->orderBy('updated_at', 'DESC')->take($this->suggestionsPost)->get();

        $this->currentCategoryId = $cid;
        $this->dispatchBrowserEvent('reinitcSlider', ['cid' => $this->category->id]);
    }

    public function selectOrder($type)
    {
        $this->orderList = $type;
        if($type == 2){
            $this->productList = Product::whereIn('category_id', $this->baseCategoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->has('minoffers2')->get()->sortBy(function ($product, $key){
                if($product->minoffers2->type < 3){
                    $offerPrice = round($product->price - (($product->price * $product->minoffers2->discount)/100), 2);
                } else {
                    $offerPrice = $product->minoffers2->discount;
                }
                return $offerPrice;

            })->take(4);
        } else {
            // $this->productList = $this->category->products()->orderBy('updated_at', 'desc')->take(4)->get();
            $this->productList = Product::whereIn('category_id', $this->baseCategoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->orderBy('updated_at', 'DESC')->take(4)->get();
        }
        $this->dispatchBrowserEvent('reinitcSlider', ['cid' => $this->category->id]);
    }
}
