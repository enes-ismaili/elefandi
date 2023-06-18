<?php

namespace App\Http\Livewire\Products;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Offer;
use App\Models\Vendor;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Arr;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class ListProducts extends Component
{
    use WithPagination;
    protected $paginationTheme = 'paginate';

    public $orderFilter = 1; 
    public $listView = 1;
    protected $vendor;
    public $pid;
    public $parent;
    public $type;
    public $baseProducts;
    public $idArray;
    public $selectedVendor = [];
    public $selectedCategory = [];
    public $selectedCategoryParent = [];
    public $selectedCategoryAll = [];
    public $minSelSlider = 0;
    public $minSlider = 0;
    public $maxSelSlider = 100;
    public $maxSlider = 100;
    public $allCategories;
    public $vendors;
    public $pageName;
    public $pageIcon;
    public $cat;
    public $swcategory = false;
    public $cNu = 1;
    
    public function mount($cat=0)
    {
        if($this->pid){
            if($this->type == 1){
                $this->pageName = 'Rezultatet e kërkimit për "'.$this->pid.'"';
                $pidss = $this->pid;
                if($this->cat){
                    if (Cache::has('cat'.$cat)) {
                        $baseCategoryIds = Cache::get('cat'.$cat);
                    } else {
                        $baseCategoryIds = Cache::rememberForever('cat'.$cat, function () {
                            $data = Category::with(['products', 'childrenRecursive'])->where('id', $this->category->id)->get()->toArray();
                            return Arr::pluck($this->flatten($data), 'id');
                        });
                    }
                    $this->selectedCategoryAll = $baseCategoryIds;

                    $this->parent = Category::find($this->cat);
                    $categoryArray = array($this->parent->id);
                    if(count($this->parent->children)){
                        foreach($this->parent->children as $catChild){
                            $newCategory = $catChild;
                            array_push($categoryArray, $newCategory->id);
                            if(count($newCategory->children)) {
                                foreach($newCategory->children as $subcatChild){
                                    array_push($categoryArray, $subcatChild->id);
                                }
                            }
                        }
                    }
                    $this->idArray = $categoryArray;
                    $this->baseProducts = Product::where(
                        function ($query) use ($pidss) {
                            $query->where('name', 'LIKE', '%'.$pidss.'%');
                        }
                    )->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('category_id', $baseCategoryIds)->get();
                    $this->swcategory = true;
                } else {
                    $this->baseProducts = Product::where(
                        function ($query) use ($pidss) {
                            $query->where('name', 'LIKE', '%'.$pidss.'%');
                        }
                    )->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->get();
                }
                $this->vendors = Vendor::select('id','name')->whereIn('id', $this->baseProducts->pluck('vendor_id'))->get();
            } else if($this->type == 2){
                $this->parent = Category::find($this->pid);
                $this->pageName = $this->parent->name;
                if($this->parent->icon){
                    $this->pageIcon = '<i class="'.$this->parent->icon.'"></i>';
                } elseif ($this->parent->image){
                    $this->pageIcon = '<img src="'.asset('photos/taxonomy/'.$this->parent->image).'" height="25">';
                }
                $categoryArray = array($this->parent->id);
                if(count($this->parent->children)){
                    $newCategory = $this->parent->children;
                    if($newCategory){
                        foreach($newCategory as $nCategory){
                            array_push($categoryArray, $nCategory->id);
                            if(count($nCategory->children)) {
                                foreach($nCategory->children as $nnCategory){
                                    array_push($categoryArray, $nnCategory->id);
                                }
                            }
                        }
                    }
                }
                $this->idArray = $categoryArray;
                $this->baseProducts = Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('category_id', $categoryArray)->get();
                $this->vendors = Vendor::select('id','name')->whereIn('id', $this->baseProducts->pluck('vendor_id'))->get();
            } else if($this->type == 3){
                $thisTag = Tag::find($this->pid);
                $this->parent = $thisTag;
                $this->pageName = $thisTag->name;
                if($this->parent->icon){
                    $this->pageIcon = '<i class="'.$this->parent->icon.'"></i>';
                } elseif ($this->parent->image){
                    $this->pageIcon = '<img src="'.asset('photos/taxonomy/'.$this->parent->image).'" height="25">';
                }
                $this->baseProducts = $thisTag->products;
                $this->vendors = Vendor::select('id','name')->whereIn('id', $this->baseProducts->pluck('vendor_id'))->get();
            } else if ($this->type == 4) {
                $thisBrand = Brand::find($this->pid);
                $this->parent = $thisBrand;
                $this->pageName = $thisBrand->name;
                if($this->parent->icon){
                    $this->pageIcon = '<i class="'.$this->parent->icon.'"></i>';
                } elseif ($this->parent->image){
                    $this->pageIcon = '<img src="'.asset('photos/taxonomy/'.$this->parent->image).'" height="25">';
                }
                $this->baseProducts = $thisBrand->products;
                $this->vendors = Vendor::select('id','name')->whereIn('id', $this->baseProducts->pluck('vendor_id'))->get();
            } else if ($this->type == 5) {
                $thisOffer = Offer::find($this->pid);
                $this->parent = $thisOffer;
                $this->pageName = $thisOffer->name;

                // dd($thisOffer->details()->with('product')->get()->pluck('product'));
                // $this->baseProducts = $thisOffer->details()->with('product')->get();
                $this->baseProducts = $thisOffer->details()->with('product')->get()->pluck('product');
                $this->vendors = Vendor::select('id','name')->whereIn('id', $this->baseProducts->pluck('vendor_id'))->get();
            }
            if($this->type == 5){
                $prices = $this->parent->details()->get()->pluck('discount')->toArray();
            } else {
                $prices = $this->baseProducts->pluck('price')->toArray();
            }
            if($prices){
                $this->minSelSlider = floor(min($prices));
                $this->minSlider = floor(min($prices));
                $this->maxSelSlider = round(max($prices));
                $this->maxSlider = round(max($prices));
            } else {
                $this->minSelSlider = 0;
                $this->minSlider = 0;
                $this->maxSelSlider = 0;
                $this->maxSlider = 0;
            }
        }
        $this->allCategories = Category::where('parent', '0')->get();
        if($cat){
            $this->selectedCategory[] = $cat;
        }
    }
    
    public function render()
    {
        $products = [];
        if($this->orderFilter == 1){
            $orderBy = 'updated_at';
        } elseif($this->orderFilter == 2){
            $orderBy = 'offerC';
        } elseif($this->orderFilter == 3){
            $orderBy = 'price';
        } elseif($this->orderFilter == 4){
            $orderBy = 'pricea';
        }
        // if($this->selectedCategory){
        //     $this->selectedCategoryAll = [];
        //     foreach($this->selectedCategory as $category){
        //         $currCategory = Category::where('id', $category)->first();
        //         $this->selectedCategoryAll[] = $currCategory->id;
        //         if($currCategory && $currCategory->parent){
        //             $this->selectedCategoryParent[] = $currCategory->parent;
        //             $this->selectedCategoryAll[] = $currCategory->parent;
        //             $currPCategory = Category::where('id', $currCategory->parent)->first();
        //             if($currPCategory && $currPCategory->parent){
        //                 $this->selectedCategoryParent[] = $currPCategory->parent;
        //                 $this->selectedCategoryAll[] = $currPCategory->parent;
        //             }
        //         }
        //     }
        //     // ray($this->selectedCategoryAll);
        // }
        $products = $this->changePosts($orderBy, $paginate=16);
        if($this->minSlider < $this->minSelSlider){
            $this->minSlider = $this->minSelSlider;
        }
        if($this->maxSlider > $this->maxSelSlider){
            $this->maxSlider = $this->maxSelSlider;
        }
        if($this->minSlider > $this->maxSlider){
            $minSlider = $this->minSlider;
            $maxSlider = $this->maxSlider;
            $this->minSlider = $maxSlider;
            $this->maxSlider = $minSlider;
        }
        $this->cNu +=1;
        return view('livewire.products.list-products', [
            'products' => $products
        ]);
    }

    public function changePosts($order, $paginate=16)
    {
        $idsArray = $this->idArray;
        $selectedVendors = $this->selectedVendor;
        $selectedCategoryAll = $this->selectedCategoryAll;
        $minPrice = $this->minSlider;
        $maxPrice = $this->maxSlider;
        if($maxPrice < 0.1) {
            $maxPrice = 0.1;
        }
        $taxonomy = 'categories';
        $taxonomyId = 'category_id';
        if($this->type == 3){
            $taxonomy = 'tags';
            $taxonomyId = 'tag_id';
        } else if($this->type == 4) {
            $taxonomy = 'brands';
            $taxonomyId = 'brand_id';
        }
        if($this->type == 1){
            $pidss = $this->pid;
            if($order == 'offerC'){
                if($selectedVendors){
                    if($this->swcategory){
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')->whereIn('category_id', $selectedCategoryAll)
                        ->has('minoffers2')->whereIn('vendor_id', $selectedVendors)->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->has('minoffers2')->whereIn('vendor_id', $selectedVendors)->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                    }
                } else {
                    if($this->swcategory){
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')->whereIn('category_id', $selectedCategoryAll)
                        ->has('minoffers2')->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->has('minoffers2')->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                    }
                }
            } elseif ($order == 'pricea'){
                if($selectedVendors){
                    if($this->swcategory){
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->whereIn('category_id', $selectedCategoryAll)
                        ->whereIn('vendor_id', $selectedVendors)->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->whereIn('vendor_id', $selectedVendors)->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                    }
                } else {
                    if($this->swcategory){
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->whereIn('category_id', $selectedCategoryAll)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where('name', 'LIKE', '%'.$pidss.'%')
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                    }
                }
            } else {
                if($selectedVendors){
                    if($this->swcategory){
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->where('name', 'LIKE', '%'.$pidss.'%')->whereIn('category_id', $selectedCategoryAll)
                        ->whereIn('vendor_id', $selectedVendors)->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->where('name', 'LIKE', '%'.$pidss.'%')->whereIn('vendor_id', $selectedVendors)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                    }
                } else {
                    if($this->swcategory){
                        ray($selectedCategoryAll);
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->where('name', 'LIKE', '%'.$pidss.'%')->whereIn('category_id', $selectedCategoryAll)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                    } else {
                        return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->where('name', 'LIKE', '%'.$pidss.'%')
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                    }
                }
            }
        } else if($this->type == 2){
            if($order == 'offerC'){
                if($selectedVendors){
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('category_id', $idsArray)->whereIn('vendor_id', $selectedVendors)
                        ->has('minoffers2')->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                } else {
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->whereIn('category_id', $idsArray)->has('minoffers2')
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                }
            } elseif ($order == 'pricea'){
                if($selectedVendors){
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->whereIn('category_id', $idsArray)->whereIn('vendor_id', $selectedVendors)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                } else {
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('category_id', $idsArray)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                }
            } else {
                if($selectedVendors){
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('category_id', $idsArray)->whereIn('vendor_id', $selectedVendors)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                } else {
                    return Product::whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                        ->whereIn('category_id', $idsArray)
                        ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                }
            }
        } else if($this->type == 3){
            if($order == 'offerC'){
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->has('minoffers2')->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->has('minoffers2')
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                }
            } elseif ($order == 'pricea'){
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                }
            } else {
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                }
            }
        } else if($this->type == 4){
            if($order == 'offerC'){
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->has('minoffers2')->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->has('minoffers2')
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->paginate($paginate);
                }
            } elseif ($order == 'pricea'){
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderBy('price')->paginate($paginate);
                }
            } else {
                if($selectedVendors){
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->whereIn('vendor_id', $selectedVendors)
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                } else {
                    return $this->parent->products()->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                    ->where('price', '>=', $minPrice)->where('price', '<=', $maxPrice)->orderByDesc($order)->paginate($paginate);
                }
            }
        } else if($this->type == 5){
            // $this->parent->details()->with('product')->get()//
            // $check = $this->parent->details()->with('product')->get()->pluck('product')->whereHas('owner', function($q){$q->where('vstatus', '=', 1);});
            // $check = $this->parent->details()->whereHas('product', function($q){
            //     $q->whereHas('owner', function($q){
            //         $q->where('vstatus', '=', 1);
            //     });
            // })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->paginate($paginate);
            // dd($check);
            if ($order == 'pricea'){
                if($selectedVendors){
                    return $this->parent->details()->whereHas('product', function($q) use ($selectedVendors){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        })->whereIn('vendor_id', $selectedVendors);
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->orderBy('discount')->paginate($paginate);
                } else {
                    return $this->parent->details()->whereHas('product', function($q){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        });
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->orderBy('discount')->paginate($paginate);
                }
            } else if($order == 'price'){
                if($selectedVendors){
                    return $this->parent->details()->whereHas('product', function($q) use ($selectedVendors){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        })->whereIn('vendor_id', $selectedVendors);
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->orderByDesc('discount')->paginate($paginate);
                } else {
                    return $this->parent->details()->whereHas('product', function($q){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        });
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->orderByDesc('discount')->paginate($paginate);
                }
            } else {
                if($selectedVendors){
                    return $this->parent->details()->whereHas('product', function($q) use ($selectedVendors){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        })->whereIn('vendor_id', $selectedVendors);
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->paginate($paginate);
                } else {
                    // $products = $this->parent->details()->paginate($paginate);
                    // dd($products);
                    // return $products;
                    return $this->parent->details()->whereHas('product', function($q){
                        $q->whereHas('owner', function($q){
                            $q->where('vstatus', '=', 1);
                        });
                    })->where('discount', '>=', $minPrice)->where('discount', '<=', $maxPrice)->paginate($paginate);
                }
            }
        }
    }

    public function changeView($view=1)
    {
        if($view == 2){
            $this->listView = 2;
        } else {
            $this->listView = 1;
        }
    }
}
