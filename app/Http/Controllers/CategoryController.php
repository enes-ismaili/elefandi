<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function category($slug)
    {
        $category = Category::where('slug', '=', $slug)->firstOrFail();
        return view('taxonomies.category', compact('category'));
    }

    function tag($slug)
    {
        $tag = Tag::where('slug', '=', $slug)->firstOrFail();
        return view('taxonomies.tag', compact('tag'));
    }

    function brand($slug)
    {
        $brand = Brand::where('slug', '=', $slug)->firstOrFail();
        return view('taxonomies.brand', compact('brand'));
    }

    public function offer($id)
    {
        if(is_numeric($id)){
            $offer = Offer::findOrFail($id);
            return view('taxonomies.offers', compact('offer'));
        }
    }

    public function searchpost(Request $request)
    {
        $getQuery = '';
        $queryString = str_replace(' ', '+', $request->squery);
        if($request->search_categories !=0){
            $selCategory = $request->search_categories;
            return redirect()->route('search.single', ['query' => $queryString, 'cat' => $selCategory]);
        }
        return redirect()->route('search.single', ['query' => $queryString]);
    }

    public function search($query)
    {
        $queryString = str_replace('+', ' ', $query);
        $newquery = array($queryString);
        return view('taxonomies.search', compact('newquery'));
    }
}
