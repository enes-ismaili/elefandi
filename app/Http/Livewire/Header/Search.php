<?php

namespace App\Http\Livewire\Header;

use App\Models\Vendor;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Search extends Component
{
    public $categories;
    public $searchQuery;
    public $results;
    public $searchs = [];
    public $showResults = false;
    public $noResults = false;
    public $search_categories = 0;

    public function render()
    {
        return view('livewire.header.search');
    }

    public function updatedSearchQuery()
    {
        if(strlen($this->searchQuery) >= 3){
            $searchq = $this->searchQuery;
            if($this->search_categories > 0){
                $catId = $this->search_categories;
                if (Cache::has('cat'.$catId)) {
                    $baseCategoryIds = Cache::get('cat'.$catId);
                } else {
                    $baseCategoryIds = Cache::rememberForever('cat'.$catId, function () {
                        $data = Category::with(['products', 'childrenRecursive'])->where('id', $this->category->id)->get()->toArray();
                        return Arr::pluck($this->flatten($data), 'id');
                    });
                }
                $results = Product::where('name', 'LIKE', '%'.$searchq.'%')->whereIn('category_id', $baseCategoryIds)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->take(5)->get();
                $resultsN = $results->count();
                if($resultsN){
                    $this->searchs = $results;
                    $this->showResults = true;
                    $this->noResults = false;
                    $this->showMore = false;
                    if($resultsN >= 5){
                        $this->showMore = true;
                    }
                } else {
                    $this->searchs = [];
                    $this->noResults = true;
                    $this->showMore = false;
                }
            } else {
                $products = Product::with('owner')->where('name', 'LIKE', '%'.$searchq.'%')->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->limit(5)->get();
                $vendors = Vendor::where('name', 'LIKE', '%'.$searchq.'%')->where('vstatus', '=', 1)->limit(3)->get();
                $results = $vendors->merge($products)->toArray();
                $resultsN = count($results);
                if($resultsN){
                    $this->searchs = $results;
                    $this->showResults = true;
                    $this->noResults = false;
                    $this->showMore = false;
                    if($resultsN >= 5){
                        $this->showMore = true;
                    }
                } else {
                    $this->searchs = [];
                    $this->noResults = true;
                    $this->showMore = false;
                }
            }
        } else {
            $this->showResults = false;
            $this->noResults = false;
            $this->showMore = false;
        }
    }

    public function searchB()
    {
        if(strlen($this->searchQuery) >= 3){
            $queryString = str_replace(' ', '+', $this->searchQuery);
            if($this->search_categories > 0){
                redirect()->route('search.single', ['query' => $queryString, 'cat' => $this->search_categories]);
            } else {
                redirect()->route('search.single', ['query' => $queryString]);
            }
        }
    }
}
