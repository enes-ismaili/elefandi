<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\footercol1;
use App\Models\footercol2;
use App\Models\footercol3;
use App\Models\HomeFeature;
use App\Models\MobileSlider;
use Illuminate\Http\Request;
use App\Models\HomeCategorySlider;
use App\Models\HomeCategoryProduct;
use App\Models\HomeFeaturedProduct;
use App\Models\HomeTrendingCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function settingsMain()
    {
        if(check_permissions('manage_vendor')){
            $logo = Setting::where('name', '=', 'logo')->first();
            $favicon = Setting::where('name', '=', 'favicon')->first();
            $footer_logo = Setting::where('name', '=', 'footer_logo')->first();
            $invoice_logo = Setting::where('name', '=', 'invoice_logo')->first();
            $pagetitlehome = Setting::where('name', '=', 'pagetitlehome')->first();
            $pagetitle = Setting::where('name', '=', 'pagetitle')->first();
            $footertel = Setting::where('name', '=', 'footertel')->first();
            $footeraddress = Setting::where('name', '=', 'footeraddress')->first();
            $footermail = Setting::where('name', '=', 'footermail')->first();
            return view('admin.settings.main', compact('logo', 'favicon', 'footer_logo', 'invoice_logo', 'pagetitlehome', 
                'pagetitle', 'footertel', 'footeraddress', 'footermail'));
        }
        abort(404);
    }

    public function savesettingsMain(Request $request)
    {
        $validatedDate = $request->validate([
            'logo' => 'required',
            'footer' => 'required',
            'invoice' => 'required',
            'favicon' => 'required',
            'pagetitlehome' => 'required',
            'pagetitle' => 'required',
            'footertel' => 'required',
            'footeraddress' => 'required',
            'footermail' => 'required',
        ], [
            'logo.required' => 'Logo është e detyrueshme',
            'footer.required' => 'Logo në footer është e detyrueshme',
            'invoice.required' => 'Logo në invoice është e detyrueshme',
            'favicon.required' => 'Favicon është e detyrueshme',
            'pagetitlehome.required' => 'Titulli i Faqes është i detyrueshëm',
            'pagetitle.required' => 'Prapashtesa e Titullit është e detyrueshme',
            'footertel.required' => 'Telefoni është e detyrueshme',
            'footeraddress.required' => 'Adresa është e detyrueshme',
            'footermail.required' => 'Email është e detyrueshme',
        ]);
        $logo = $request->logo;
        $favicon = $request->favicon;
        $footer_logo = $request->footer;
        $invoice_logo = $request->invoice;
        $pagetitlehome = $request->pagetitlehome;
        $pagetitle = $request->pagetitle;
        $footertel = $request->footertel;
        $footeraddress = $request->footeraddress;
        $footermail = $request->footermail;
        Setting::upsert([
            ['name' => 'logo', 'value' => $logo],
            ['name' => 'favicon', 'value' => $favicon],
            ['name' => 'footer_logo', 'value' => $footer_logo],
            ['name' => 'invoice_logo', 'value' => $invoice_logo],
            ['name' => 'pagetitlehome', 'value' => $pagetitlehome],
            ['name' => 'pagetitle', 'value' => $pagetitle],
            ['name' => 'footertel', 'value' => $footertel],
            ['name' => 'footeraddress', 'value' => $footeraddress],
            ['name' => 'footermail', 'value' => $footermail],
        ], ['name'], ['value']);
        // Setting::updateOrCreate([
        //     ['name'=>'logo'],
        //     ['value'=>$logo]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'favicon'],
        //     ['value'=>$favicon]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'footer_logo'],
        //     ['value'=>$footer_logo]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'invoice_logo'],
        //     ['value'=>$invoice_logo]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'pagetitlehome'],
        //     ['value'=>$pagetitlehome]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'pagetitle'],
        //     ['value'=>$pagetitle]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'footertel'],
        //     ['value'=>$footertel]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'footeraddress'],
        //     ['value'=>$footeraddress]
        // ]);
        // Setting::updateOrCreate([
        //     ['name'=>'footermail'],
        //     ['value'=>$footermail]
        // ]);
        // Setting::updateOrCreate(
        //     [
        //         ['name'=>'logo'],
        //         ['value'=>$logo],
        //     ],
        //     [
        //         ['name'=>'favicon'],
        //         ['value'=>$favicon],
        //     ]
        // );
        // ['logo'=>$logo],
        // ['favicon'=>$favicon],
        // ['footer_logo'=>$footer_logo],
        // ['invoice_logo'=>$invoice_logo],
        // ['pagetitlehome'=>$pagetitlehome],
        // ['pagetitle'=>$pagetitle],
        // ['footertel'=>$footertel],
        // ['footeraddress'=>$footeraddress],
        // ['footermail'=>$footermail],
        Cache::forget('asettings');
        session()->put('success','Ndryshimet u rajtën me sukses.');
        return redirect()->route('admin.settings.main');
    }

    public function settingsSlider()
    {
        if(check_permissions('manage_vendor')){
            $wslider1 = Setting::where('name', '=', 'wslider1')->first();
            $wsliderLink1 = Setting::where('name', '=', 'wsliderLink1')->first();
            $wslider2 = Setting::where('name', '=', 'wslider2')->first();
            $wsliderLink2 = Setting::where('name', '=', 'wsliderLink2')->first();
            $wslider3 = Setting::where('name', '=', 'wslider3')->first();
            $wsliderLink3 = Setting::where('name', '=', 'wsliderLink3')->first();
            return view('admin.settings.home.sliders', compact('wslider1','wsliderLink1','wslider2','wsliderLink2','wslider3','wsliderLink3'));
        }
        abort(404);
    }

    public function settingsSliderUpdate(Request $request)
    {
        $validatedDate = $request->validate([
            'wslider1' => 'required',
            'wslider2' => 'required',
            'wslider3' => 'required',
        ], [
            'wslider1.required' => 'Slideri 1 është i detyrueshëm',
            'wslider2.required' => 'Slideri 2 është i detyrueshëm',
            'wslider3.required' => 'Slideri 3 është i detyrueshëm',
        ]);
        $wslider1 = $request->wslider1;
        $wsliderLink1 = $request->link1;
        $wslider2 = $request->wslider2;
        $wsliderLink2 = $request->link2;
        $wslider3 = $request->wslider3;
        $wsliderLink3 = $request->link3;
        Setting::upsert([
            ['name' => 'wslider1', 'value' => $wslider1],
            ['name' => 'wsliderLink1', 'value' => $wsliderLink1],
            ['name' => 'wslider2', 'value' => $wslider2],
            ['name' => 'wsliderLink2', 'value' => $wsliderLink2],
            ['name' => 'wslider3', 'value' => $wslider3],
            ['name' => 'wsliderLink3', 'value' => $wsliderLink3]
        ], ['name'], ['value']);
        Cache::forget('asettings');
        session()->put('success','Ndryshimet u rajtën me sukses.');
        return redirect()->route('admin.homesettings.slider');
    }

    public function settingsSliderMobile()
    {
        if(check_permissions('manage_vendor')){
            $sliders = MobileSlider::all();
            return view('admin.settings.home.mobile.sliders', compact('sliders'));
        }
        abort(404);
    }
    public function settingsSliderMobileAdd()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.settings.home.mobile.addslider');
        }
        abort(404);
    }

    public function settingsSliderMobileAddStore(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = Validator::make($request->all(), [
                'image' => 'required',
            ], [
                'image.required' => 'Imazhi është i detyrueshëm',
            ]);
			if ($validatedDate->fails()) {
                return redirect('admin.homesettings.slidermobile.add')->withErrors($validatedDate)->withInput();
            }
            $slider = new MobileSlider();
            $slider->image = $request->image;
            if($request->link){
                $currLink = $this->appLink($request->link);
                if($currLink['status'] == 'success'){
                    $slider->link = $currLink['link'];
                    $slider->olink = $request->link;
                } else {
					$validatedDate->errors()->add(
						'link', 'Linku duhet te jete i nje produkti, kategorie ose dyqani të elefandit!'
					);
					return redirect()->route('admin.homesettings.slidermobile.add')->withInput()->withErrors($validatedDate);
				}
            }
            if($request->corder > 0){
                $slider->corder = $request->corder;
            }
            $slider->save();
            session()->put('success','Slideri u shtua me sukses.');
            return redirect()->route('admin.homesettings.slidermobile');
        }
        abort(404);
    }

    public function settingsSliderMobileEdit($id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $slider = MobileSlider::findorfail($id);
            return view('admin.settings.home.mobile.editslider', compact('slider'));
        }
        abort(404);
    }

    public function settingsSliderMobileEditUpdate(Request $request, $id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $validatedDate = Validator::make($request->all(), [
                'image' => 'required',
            ], [
                'image.required' => 'Imazhi është i detyrueshëm',
            ]);
            if ($validatedDate->fails()) {
                return redirect('admin.homesettings.slidermobile.edit', $id)->withErrors($validatedDate)->withInput();
            }
            $slider = MobileSlider::findorfail($id);
            $slider->image = $request->image;
            if($request->link && ($slider->olink != $request->link)){
                $currLink = $this->appLink($request->link);
                if($currLink['status'] == 'success'){
                    $slider->link = $currLink['link'];
                    $slider->olink = $request->link;
                } else {
					$validatedDate->errors()->add(
						'link', 'Linku duhet te jete i nje produkti, kategorie ose dyqani të elefandit!'
					);
					return redirect()->route('admin.homesettings.slidermobile.edit', $id)->withInput()->withErrors($validatedDate);
				}
            }
            if($request->corder > 0){
                $slider->corder = $request->corder;
            }
            $slider->save();
            session()->put('success','Slideri u ndryshua me sukses.');
            return redirect()->route('admin.homesettings.slidermobile');
        }
        abort(404);
    }

    public function settingsSliderMobileDelete($id)
    {
        if(check_permissions('manage_vendor') && is_numeric($id)){
            $slider = MobileSlider::findorfail($id);
            $slider->delete();
            session()->put('success','Slideri u fshi me sukses.');
            return redirect()->route('admin.homesettings.slidermobile');
        }
    }

    public function appLink($link)
    {
        $re = '/^(?:(?:www\.)?(?:.*?))\.(?:com|al)\/(.*)/m';
        preg_match($re,trim($link),$match);
        if($match && count($match) && $match[1]){
            preg_match('/^(?:(?:https|http)?(?::\/\/))?(?:www.)?(elefandi.com)/m',trim($link),$matchUrl);
            if($matchUrl && $matchUrl[1] != 'elefandi.com'){
                return ['status'=>'error', 'message'=>'Link i gabuar1'];
            }
            $urlExplode = explode('/', $match[1]);
            if(count($urlExplode) == 2){
                if(is_numeric($urlExplode[1])){
                    $pId = $urlExplode[1];
                    $product = Product::findOrFail($pId);
                    if($product){
                        return ['status'=>'success', 'link'=>'product/'.$pId];
                    }
                    return $product;
                } else {
                    if($urlExplode[0] == 'category'){
                        $cSlug = $urlExplode[1];
                        $category = Category::where('slug', '=', $cSlug)->first();
                        if($category){
                            return ['status'=>'success', 'link'=>'category/'.$category->id];
                        }
                    }
                }
            } else {
                $vendor = Vendor::where('slug', '=', $urlExplode[0])->first();
                if($vendor){
                    return ['status'=>'success', 'link'=>'vendor/'.$vendor->id];
                }
            }
        }
        return ['status'=>'error', 'message'=>'Link i gabuar1'];
    }

    public function settingsFeatures()
    {
        if(check_permissions('manage_vendor')){
            $features = HomeFeature::orderBy('corder')->get();
            return view('admin.settings.home.features', compact('features'));
        }
        abort(404);
    }

    public function addFeatures()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.settings.home.addfeatures');
        }
        abort(404);
    }

    public function storeFeatures(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'required_unless:imageFeature,1',
                'icon' => 'required_unless:imageFeature,0',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'image.required_unless' => 'Imazhi është i detyrueshëm',
                'icon.required_unless' => 'Ikona është e detyrueshme',
            ]);
            $allFeatures = HomeFeature::count();
            if($allFeatures < 4){
                $feature = new HomeFeature();
                $feature->name = $request->name;
                $feature->description = $request->description;
                if($request->imageFeature == 1){
                    $feature->icon = $request->icon;
                } else {
                    $feature->image = $request->image;
                }
				$feature->corder = $request->corder;
                $feature->save();
                session()->put('success','Vecoria u ruajt me sukses.');
                return redirect()->route('admin.homesettings.features');
            } else {
                session()->put('error','Maksimumi i Veçorive është arritur. Fshi nje nga veçoritë për të shtuar të re.');
                return redirect()->route('admin.homesettings.features');
            }
        }
        abort(404);
    }

    public function editFeatures($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $feature = HomeFeature::findorfail($id);
                return view('admin.settings.home.editfeatures', compact('feature'));
            }
        }
        abort(404);
    }

    public function saveFeatures(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'name' => 'required',
                    'description' => 'required',
                    'image' => 'required_unless:imageFeature,1',
                    'icon' => 'required_unless:imageFeature,0',
                ], [
                    'name.required' => 'Emri është i detyrueshëm',
                    'description.required' => 'Përshkrimi është i detyrueshëm',
                    'image.required_unless' => 'Imazhi është i detyrueshëm',
                    'icon.required_unless' => 'Ikona është e detyrueshme',
                ]);
                $feature = HomeFeature::findorfail($id);
                $feature->name = $request->name;
                $feature->description = $request->description;
                if($request->imageFeature == 1){
                    $feature->icon = $request->icon;
                    $feature->image = '';
                } else {
                    $feature->icon = '';
                    $feature->image = $request->image;
                }
				$feature->corder = $request->corder;
                $feature->save();
                session()->put('success','Vecoria u ruajt me sukses.');
                return redirect()->route('admin.homesettings.features');
            }
        }
        abort(404);
    }

    public function deleteFeatures($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $feature = HomeFeature::findorfail($id);
                $feature->delete();
                session()->put('success','Vecoria u fshi me sukses.');
                return redirect()->route('admin.homesettings.features');
            }
        }
        abort(404);
    }

    public function featuredProduct()
    {
        if(check_permissions('manage_vendor')){
            $featured = HomeFeaturedProduct::orderBy('corder')->get();
            return view('admin.settings.home.featured', compact('featured'));
        }
        abort(404);
    }

    public function addFeaturedProduct()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.settings.home.addfeatured');
        }
        abort(404);
    }

    public function storeFeaturedProduct(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'image' => 'required',
                'button' => 'required',
                'link' => 'required',
                'corder' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'image.required_unless' => 'Imazhi është i detyrueshëm',
                'icon.required_unless' => 'Ikona është e detyrueshme',
                'button.required_unless' => 'Emri i Butonit është i detyrueshëm',
                'link.required_unless' => 'Linku është i detyrueshëm',
                'corder.required_unless' => 'Nr i Renditjes është i detyrueshëm',
            ]);
            $allFeatures = HomeFeaturedProduct::count();
            if($allFeatures < 3){
                $feature = new HomeFeaturedProduct();
                $feature->name = $request->name;
                $feature->image = $request->image;
                $feature->button = $request->button;
                $feature->link = $request->link;
                $feature->corder = $request->corder;
                $feature->save();
                session()->put('success','Shtimi i porduktit të preferuar u krye me sukses.');
                return redirect()->route('admin.homesettings.featuredProduct');
            } else {
                session()->put('error','Maksimumi i Veçorive është arritur. Fshi nje nga veçoritë për të shtuar të re.');
                return redirect()->route('admin.homesettings.features');
            }
        }
        abort(404);
    }

    public function editFeaturedProduct($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $featured = HomeFeaturedProduct::findorfail($id);
                return view('admin.settings.home.editfeatured', compact('featured'));
            }
        }
        abort(404);
    }

    public function saveFeaturedProduct(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'name' => 'required',
                    'image' => 'required',
                    'button' => 'required',
                    'link' => 'required',
                    'corder' => 'required',
                ], [
                    'name.required' => 'Emri është i detyrueshëm',
                    'image.required_unless' => 'Imazhi është i detyrueshëm',
                    'button.required_unless' => 'Emri i Butonit është i detyrueshëm',
                    'link.required_unless' => 'Linku është i detyrueshëm',
                    'corder.required_unless' => 'Nr i Renditjes është i detyrueshëm',
                ]);
                $feature = HomeFeaturedProduct::findorfail($id);
                $feature->name = $request->name;
                $feature->image = $request->image;
                $feature->button = $request->button;
                $feature->link = $request->link;
                $feature->corder = $request->corder;
                $feature->save();
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.homesettings.featuredProduct');
            }
        }
        abort(404);
    }

    public function deleteFeaturedProduct($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $featured = HomeFeaturedProduct::findorfail($id);
                $featured->delete();
                session()->put('success','Produkti i perzgjedhur u fshi me sukses.');
                return redirect()->route('admin.homesettings.featuredProduct');
            }
        }
        abort(404);
    }

    public function trendingCategories()
    {
        if(check_permissions('manage_vendor')){
            $categories = Category::where('parent', '0')->get();
            return view('admin.settings.home.trendingcat', compact('categories'));
        }
        abort(404);
    }

    public function editrendingCategories($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $category = Category::findorfail($id);
                return view('admin.settings.home.trendingcatedit', compact('category'));
            }
        }
        abort(404);
    }

    public function storeTrendingCategories(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $category = Category::findorfail($id);
                $category->trending = $request->trending;
                $category->save();
                $deletedOfferDetail = HomeTrendingCategory::where([
                    ['category_id', '=', $id],
                ])->delete();
                $i=0;
                if($request->tagid){
                    foreach($request->tagid as $tag){
                        $i++;
                        $trendingCategory = HomeTrendingCategory::updateOrCreate(
                            ['category_id' => $id, 'tag_id'=> $tag],
                            ['corder' => $i]
                        );
                    }
                }
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.homesettings.trending.index');
            }
        }
        abort(404);
    }

    public function categoriesHome()
    {
        if(check_permissions('manage_vendor')){
            $categories = Category::where('parent', '0')->get();
            return view('admin.settings.home.categories', compact('categories'));
        }
        abort(404);
    }

    public function editCategoriesHome($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $category = Category::findorfail($id);
                return view('admin.settings.home.categoriesedit', compact('category'));
            }
        }
        abort(404);
    }

    public function storeCategoriesHome(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $category = Category::findorfail($id);
                $category->home = $request->home;
                $category->save();
                $deletedOfferDetail = HomeCategoryProduct::where([
                    ['category_id', '=', $id],
                ])->delete();
                $i=0;
                if($request->catid){
                    foreach($request->catid as $cat){
                        $i++;
                        $trendingCategory = HomeCategoryProduct::updateOrCreate(
                            ['category_id' => $id, 'children_id'=> $cat],
                            ['corder' => $i]
                        );
                    }
                }
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.homesettings.categories.index');
            }
        }
        abort(404);
    }

    public function sliderCategoriesHome($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $category = Category::findorfail($id);
                return view('admin.settings.home.categoriesslideradd', compact('category'));
            }
        }
        abort(404);
    }

    public function sliderAddCategoriesHome(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'image' => 'required',
                ], [
                    'image.required' => 'Foto Slideri është i detyrueshëm',
                ]);
                $category = Category::findorfail($id);
                $categorySlider = new HomeCategorySlider();
                $categorySlider->category_id = $id;
                $categorySlider->image = $request->image;
                $categorySlider->link = $request->link;
                $categorySlider->corder = $request->corder;
                $categorySlider->save();
                session()->put('success','Slideri u shtua me sukses.');
                return redirect()->route('admin.homesettings.categories.edit', $category->id);
            }
        }
        abort(404);
    }

    public function editsliderCategoriesHome($id, $sid)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id) && is_numeric($sid)){
                $category = Category::findorfail($id);
                $slider = HomeCategorySlider::findorfail($sid);
                return view('admin.settings.home.categoriesslideredit', compact('category', 'slider'));
            }
        }
        abort(404);
    }

    public function sliderStoreCategoriesHome(Request $request, $id, $sid)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id) && is_numeric($sid)){
                $validatedDate = $request->validate([
                    'image' => 'required',
                ], [
                    'image.required' => 'Foto Slideri është i detyrueshëm',
                ]);
                $category = Category::findorfail($id);
                $categorySlider = HomeCategorySlider::findorfail($sid);
                $categorySlider->category_id = $id;
                $categorySlider->image = $request->image;
                $categorySlider->link = $request->link;
                $categorySlider->corder = $request->corder;
                $categorySlider->save();
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.homesettings.categories.edit', $category->id);
            }
        }
        abort(404);
    }

    public function sliderDeleteCategoriesHome($id, $sid)
    {
        if(check_permissions('manage_vendor') && check_permissions('delete_rights')){
            if(is_numeric($id) && is_numeric($sid)){
                $slider = HomeCategorySlider::findorfail($sid);
                if($slider->category_id == $id){
                    $slider->delete();
                    session()->put('success','Slideri u fshi me sukses.');
                }
                return redirect()->route('admin.homesettings.categories.edit', $id);
            }
        }
        abort(404);
    }

    public function countries()
    {
        if(check_permissions('manage_vendor')){
            $countries = Country::where('shipping', '=', 1)->get();
            return view('admin.settings.countries.index', compact('countries'));
        }
        abort(404);
    }

    public function editcountries($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $country = Country::findorfail($id);
                return view('admin.settings.countries.edit-country', compact('country'));
            }
        }
        abort(404);
    }

    public function updatecountries(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'cities.*' => 'required',
                ]);
                $i=0;
                foreach($request->cities as $city){
                    $i++;
                    City::findOrFail($city)->update(['corder' => $i]);
                }
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.settings.countries.index');
            }
        }
        abort(404);
    }

    public function addcities($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $country = Country::findorfail($id);
                return view('admin.settings.countries.add-city', compact('country'));
            }
        }
        abort(404);
    }

    public function storecities(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'name' => 'required',
                ], [
                    'name.required' => 'Emri është i detyrueshëm',
                ]);
                $city = new City();
                $city->name = $request->name;
                $city->country_id = $id;
                $city->save();
                session()->put('success','Qyteti u shtua me sukses.');
                return redirect()->route('admin.settings.countries.edit', $id);
            }
        }
        abort(404);
    }

    public function editcities($id, $cid)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id) && is_numeric($cid)){
                $city = City::findorfail($cid);
                if($city->country_id == $id){
                    $country = Country::findorfail($id);
                    return view('admin.settings.countries.edit-city', compact('country', 'city'));
                }
            }
        }
        abort(404);
    }

    public function updatecities(Request $request, $id, $cid)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id) && is_numeric($cid)){
                $city = City::findorfail($cid);
                if($city->country_id == $id){
                    $city->name = $request->name;
                    $city->save();
                    session()->put('success','Ndryshimet u ruajtën me sukses.');
                    return redirect()->route('admin.settings.countries.edit', $id);
                }
            }
        }
        abort(404);
    }
    public function deletecities($id, $cid)
    {
        if(check_permissions('manage_vendor') && check_permissions('delete_rights')){
            if(is_numeric($id) && is_numeric($cid)){
                $city = City::findorfail($cid);
                if($city->country_id == $id){
                    $city->status = 0;
                    $city->save();
                    session()->put('warning','Qyteti u fshi me sukses.');
                    return redirect()->route('admin.settings.countries.edit', $id);
                }
            }
        }
        abort(404);
    }

    public function footercolumn($id)
    {
        if(is_numeric($id)){
            if($id == 1){
                $footer = footercol1::orderBy('corder', 'asc')->get();
                $colNr = 1;
            } else if($id == 2){
                $footer = footercol2::orderBy('corder', 'asc')->get();
                $colNr = 2;
            } else if($id == 3){
                $footer = footercol3::orderBy('corder', 'asc')->get();
                $colNr = 3;
            }
            return view('admin.settings.footer.index', compact('footer', 'colNr'));
        }
    }

    public function footercolumn1()
    {
        $footer = footercol1::orderBy('corder', 'asc')->get();
        $colNr = 1;
        return view('admin.settings.footer.index', compact('footer', 'colNr'));
    }
    public function footercolumn2()
    {
        $footer = footercol2::orderBy('corder', 'asc')->get();
        $colNr = 2;
        return view('admin.settings.footer.index', compact('footer', 'colNr'));
    }
    public function footercolumn3()
    {
        $footer = footercol3::orderBy('corder', 'asc')->get();
        $colNr = 3;
        return view('admin.settings.footer.index', compact('footer', 'colNr'));
    }

    public function footeradd($id)
    {
        if(is_numeric($id)){
            return view('admin.settings.footer.add', compact('id'));
        }
    }

    public function footerstore($id, Request $request)
    {
        if(is_numeric($id)){
            if($id == 1){
                $menu = new footercol1();
            } else if($id == 2){
                $menu = new footercol2();
            } else if($id == 3){
                $menu = new footercol3();
            }
            if($menu){
                $menu->name = $request->name;
                $menu->link = $request->link;
                $menu->save();
                session()->put('success','Linku u shtua me sukses.');
                return redirect()->route('admin.settings.footer.index', $id);
            }
        }
    }

    public function footeredit($lid, $id)
    {
        if(is_numeric($lid) && is_numeric($id)){
            if($lid == 1){
                $link = footercol1::findOrFail($id);
            } else if($lid == 2){
                $link = footercol2::findOrFail($id);
            } else if($lid == 3){
                $link = footercol3::findOrFail($id);
            }
            if($link){
                return view('admin.settings.footer.edit', compact('lid', 'id', 'link'));
            }
        }
        abort(404);
    }

    public function footerupdate($lid, $id, Request $request)
    {
        if(is_numeric($lid) && is_numeric($id)){
            if($lid == 1){
                $menu = footercol1::findOrFail($id);
            } else if($lid == 2){
                $menu = footercol2::findOrFail($id);
            } else if($lid == 3){
                $menu = footercol3::findOrFail($id);
            }
            if($menu){
                $menu->name = $request->name;
                $menu->link = $request->link;
                $menu->save();
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.settings.footer.index', $lid);
            }
        }
    }

    public function footerorder(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $validatedDate = $request->validate([
                    'links.*' => 'required',
                ]);
                $i=0;
                if($id == 1){
                    foreach($request->links as $link){
                        $i++;
                        footercol1::findOrFail($link)->update(['corder' => $i]);
                    }
                } else if($id == 2){
                    foreach($request->links as $link){
                        $i++;
                        footercol2::findOrFail($link)->update(['corder' => $i]);
                    }
                } else if($id == 3){
                    foreach($request->links as $link){
                        $i++;
                        footercol3::findOrFail($link)->update(['corder' => $i]);
                    }
                }
                session()->put('success','Ndryshimet u ruajtën me sukses.');
                return redirect()->route('admin.settings.footer.index', $id);
            }
        }
        abort(404);
    }

    public function footerdelete($lid, $id)
    {
        if(is_numeric($lid) && is_numeric($id)){
            if($lid == 1){
                $menu = footercol1::findOrFail($id);
            } else if($lid == 2){
                $menu = footercol2::findOrFail($id);
            } else if($lid == 3){
                $menu = footercol3::findOrFail($id);
            }
            if($menu){
                $menu->delete();
                session()->put('success','Menu u fshi me sukses.');
                return redirect()->route('admin.settings.footer.index', $lid);
            }
        }
    }
}
