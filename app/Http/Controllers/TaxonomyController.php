<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Variant;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class TaxonomyController extends Controller
{
    public function categories()
    {
        if(check_permissions('manage_products')){
            $categories = Category::where('parent', '0')->orderBy('corder')->get();
            return view('admin.products.categories.category-index', compact('categories'));
        }
        abort(404);
    }

    public function savecategories(Request $request)
    {
        if(check_permissions('manage_products')){
            $allCategories = json_decode($request->categories);
            $catNum = 0;
            foreach($allCategories as $mainCategory){
                $catNum++;
                Category::findOrFail($mainCategory[0])->update(['corder' => $catNum]);
                // print_r($mainCategory);
                $subCategoryNum = 0;
                foreach($mainCategory[1] as $subCategory){
                    $subCategoryNum++;
                    Category::findOrFail($subCategory[0])->update(['corder' => $subCategoryNum]);
                    $subSubCategoryNum = 0;
                    foreach($subCategory[1] as $subSubCategory){
                        $subSubCategoryNum++;
                        Category::findOrFail($subSubCategory)->update(['corder' => $subSubCategoryNum]);
                    }
                }
            }
            return array('status'=>'success');
        }
        return array('status'=>'error');
        abort(404);
    }

    public function addcategory()
    {
        if(check_permissions('manage_products')){
            $categories = Category::where('parent', '0')->orderBy('corder')->get();
            return view('admin.products.categories.category-add', compact('categories'));
        }
        abort(404);
    }

    public function storecategory(Request $request)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'parent' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'parent.required' => 'Kategoria prind është e detyrueshme',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->parent = $request->parent;
            if($request->iconType){
                $category->image = $request->image;
            } else {
                $category->icon = $request->icon;
            }
            $category->save();
            $scategory = Category::where('id', '=', $request->parent)->first();
            if($scategory && $scategory->parents){
                Cache::forget('pcat');
                if($scategory->parents->parents){
                    Cache::forget('cat'.$scategory->parents->parents->id);
                } else {
                    Cache::forget('cat'.$scategory->parents->id);
                }
            }
            session()->put('success','Kategoria u shtua me sukses.');
            return redirect()->route('admin.products.categories.index');
        }
        abort(404);
    }

    public function editcategory($id)
    {
        if(check_permissions('manage_products')){
            $scategory = Category::findorfail($id);
            $categories = Category::where('parent', '0')->orderBy('corder')->get();
            return view('admin.products.categories.category-edit', compact('scategory', 'categories'));
        }
        abort(404);
    }

    public function updatecategory(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'parent' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'parent.required' => 'Kategoria prind është e detyrueshme',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $category = Category::findorfail($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->slug = '';
            $category->parent = $request->parent;
            if($request->iconType){
                $category->image = $request->image;
                $category->icon = NULL;
            } else {
                $category->image = NULL;
                $category->icon = $request->icon;
            }
            $category->save();
            $scategory = Category::where('id', '=', $request->parent)->first();
            if($scategory && $scategory->parents){
                Cache::forget('pcat');
                if($scategory->parents->parents){
                    Cache::forget('cat'.$scategory->parents->parents->id);
                } else {
                    Cache::forget('cat'.$scategory->parents->id);
                }
            } else {
                Cache::forget('pcat');
                Cache::forget('cat'.$id);
            }
            session()->put('success','Kategoria u ndryshua me sukses.');
            return redirect()->route('admin.products.categories.index');
        }
        abort(404);
    }

    public function deletecategory($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $category = Category::findorfail($id);
            if($category){
                $category->delete();
                // TODO: Notification
                session()->put('success','Kategoria u fshi me sukses.');
                return redirect()->route('admin.products.categories.index');
            }
        }
        abort(404);
    }

    public function tags()
    {
        if(check_permissions('manage_products')){
            $tags = Tag::all();
            return view('admin.products.tags.index', compact('tags'));
        }
        abort(404);
    }

    public function addtag()
    {
        if(check_permissions('manage_products')){
            return view('admin.products.tags.add');
        }
        abort(404);
    }

    public function storetag(Request $request)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->description = $request->description;
            if($request->iconType){
                $tag->image = $request->image;
            } else {
                $tag->icon = $request->icon;
            }
            $tag->save();
            session()->put('success','Tag-u u shtua me sukses.');
            return redirect()->route('admin.products.tags.index');
        }
        abort(404);
    }

    public function edittag($id)
    {
        if(check_permissions('manage_products')){
            $stag = Tag::findorfail($id);
            if($stag){
                return view('admin.products.tags.edit', compact('stag'));
            }
        }
        abort(404);
    }

    public function updatetag(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $tag = Tag::findorfail($id);
            $tag->name = $request->name;
            $tag->description = $request->description;
            $tag->slug = '';
            if($request->iconType){
                $tag->image = $request->image;
                $tag->icon = NULL;
            } else {
                $tag->image = NULL;
                $tag->icon = $request->icon;
            }
            $tag->save();
            session()->put('success','Tag-u u ndryshua me sukses.');
            return redirect()->route('admin.products.tags.index');
        }
        abort(404);
    }

    public function deletetag($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $tag = Tag::findorfail($id);
            if($tag){
                $tag->delete();
                
                session()->put('success','Tag-u u fshi me sukses.');
                return redirect()->route('admin.products.tags.index');
            }
        }
        abort(404);
    }

    public function brands()
    {
        if(check_permissions('manage_products')){
            $brands = Brand::all();
            return view('admin.products.brands.index', compact('brands'));
        }
        abort(404);
    }

    public function addbrand()
    {
        if(check_permissions('manage_products')){
            return view('admin.products.brands.add');
        }
        abort(404);
    }

    public function storebrand(Request $request)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->description = $request->description;
            if($request->iconType){
                $brand->image = $request->image;
            } else {
                $brand->icon = $request->icon;
            }
            $brand->save();
            session()->put('success','Brand-i u shtua me sukses.');
            return redirect()->route('admin.products.brands.index');
        }
        abort(404);
    }

    public function editbrand($id)
    {
        if(check_permissions('manage_products')){
            $sbrand = Brand::findorfail($id);
            if($sbrand){
                return view('admin.products.brands.edit', compact('sbrand'));
            }
        }
        abort(404);
    }

    public function updatebrand(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'iconType' => 'required',
                'image' => 'required_unless:iconType,0',
                'icon' => 'required_unless:iconType,1',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'iconType.required' => 'Tipi është i detyrueshëm',
                'image.required_unless' => 'Foto është e detyrueshme',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $brand = Brand::findorfail($id);
            $brand->name = $request->name;
            $brand->description = $request->description;
            $brand->slug = '';
            if($request->iconType){
                $brand->image = $request->image;
                $brand->icon = NULL;
            } else {
                $brand->image = NULL;
                $brand->icon = $request->icon;
            }
            $brand->save();
            session()->put('success','Brand-i u ndryshua me sukses.');
            return redirect()->route('admin.products.brands.index');
        }
        abort(404);
    }

    public function deletebrand($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $brand = Brand::findorfail($id);
            if($brand){
                $brand->delete();
                session()->put('success','Brand-i u fshi me sukses.');
                return redirect()->route('admin.products.brands.index');
            }
        }
        abort(404);
    }

    public function variants()
    {
        if(check_permissions('manage_products')){
            $variants = Variant::all();
            return view('admin.products.variants.index', compact('variants'));
        }
        abort(404);
    }

    public function addvariant()
    {
        if(check_permissions('manage_products')){
            return view('admin.products.variants.add');
        }
        abort(404);
    }

    public function storevariant(Request $request)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $variant = new Variant();
            $variant->name = $request->name;
            $variant->save();
            session()->put('success','Varianti u shtua me sukses.');
            return redirect()->route('admin.products.variants.index');
        }
        abort(404);
    }

    public function editvariant($id)
    {
        if(check_permissions('manage_products')){
            $svariant = Variant::findorfail($id);
            if($svariant && $svariant->dshow == 1){
                return view('admin.products.variants.edit', compact('svariant'));
            }
        }
        abort(404);
    }

    public function updatevariant(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $variant = Variant::findorfail($id);
            if($variant && $variant->dshow == 1){
                $variant->name = $request->name;
                $variant->save();
                session()->put('success','Varianti u ndryshua me sukses.');
                return redirect()->route('admin.products.variants.index');
            }
        }
        abort(404);
    }

    public function deletevariant($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $variant = Variant::findorfail($id);
            if($variant && $variant->dshow == 1){
                $variant->delete();
                session()->put('success','Varianti u fshi me sukses.');
                return redirect()->route('admin.products.variants.index');
            }
        }
        abort(404);
    }

    public function colors()
    {
        if(check_permissions('manage_products')){
            $colors = Color::all();
            return view('admin.products.colors.index', compact('colors'));
        }
        abort(404);
    }

    public function addcolor()
    {
        if(check_permissions('manage_products')){
            return view('admin.products.colors.add');
        }
        abort(404);
    }

    public function storecolor(Request $request)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $color = new Color();
            $color->name = $request->name;
            $color->slug = '';
            $color->save();
            session()->put('success','Ngjyra u shtua me sukses.');
            return redirect()->route('admin.products.colors.index');
        }
        abort(404);
    }

    public function editcolor($id)
    {
        if(check_permissions('manage_products')){
            $scolor = Color::findorfail($id);
            if($scolor){
                return view('admin.products.colors.edit', compact('scolor'));
            }
        }
        abort(404);
    }

    public function updatecolor(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $color = Color::findorfail($id);
            if($color){
                $color->name = $request->name;
                $color->slug = '';
                $color->save();
                session()->put('success','Ngjyra u ndryshua me sukses.');
                return redirect()->route('admin.products.colors.index');
            }
        }
        abort(404);
    }

    public function deletecolor($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $color = Color::findorfail($id);
            if($color){
                $color->delete();
                session()->put('success','Ngjyra u fshi me sukses.');
                return redirect()->route('admin.products.colors.index');
            }
        }
        abort(404);
    }
}
