<?php

namespace App\Http\Controllers;

use App\Models\Ads;
use App\Models\AdsSingle;
use App\Models\Tag;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Country;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;
use App\Models\StoryItem;
use App\Models\HomeFeature;
use Illuminate\Support\Str;
use App\Models\ProductSales;
use Illuminate\Http\Request;
use App\Models\ProductRating;
use App\Models\ProductGallery;
use App\Models\ProductReports;
use App\Models\ProductVariant;
use App\Models\ProductShipping;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
    public function index($vendorSlug, $id)
    {
        $product = Product::findorfail($id);
        if(!$product->qrcode){
            $qrName = $product->id;
            $qrExtension = '.png';
            $exists = Storage::disk('local')->exists('photos/qrcodes/products/'.$qrName.$qrExtension);
            if ($exists) {
                $increment = 0;
                $this->name = $qrName.$qrExtension;
                while(Storage::disk('local')->exists('photos/qrcodes/products/'.$qrName.$qrExtension)) {
                    $increment++;
                    $qrName = $qrName.'-'.$increment.$qrExtension;
                }
            }
            $qrPath = 'photos/qrcodes/products/'.$qrName.$qrExtension;
            $productLink = route('single.product', [$product->owner->slug, $product->id]);
            // QrCode::size(150)->generate($productLink, $qrPath);
            QrCode::format('png')->merge('https://new57.elefandi.com/images/qr-icon.png', .2, true)->size(150)->generate($productLink, $qrPath);
            $product->qrcode = $qrName.$qrExtension;
            $product->save();
        }
        // $product = Product::where('slug', '=', $slug)->firstOrFail();
        $features = HomeFeature::orderBy('corder', 'asc')->limit(4)->get();
        // ray(current_user()->orders()->with('detailsId')->has('detailsId')->get());
        $hasBuy = 0;
        if($product->owner->slug == $vendorSlug && $product->owner->vstatus == 1){
            $productIdd = $product->id;
            if(current_user() && current_user()->ratings->where('product_id', $productIdd)->count() == 0){
                $hasBuy = current_user()->orders()->withCount(['details' => function ($q) use($productIdd){
                    $q->where('product_id', '=', $productIdd);
                }])->get()->where('details_count', '!=', 0)->count();
            }
            $shippingCountry = Country::where('shipping', '1')->get();
            $Ads = Ads::find(2);
            $singleAds = false;
            if($Ads){
                if(!$Ads->ads->where('astatus', '=', 1)->isEmpty()){
                    $singleAds = $Ads->ads->where('astatus', '=', 1)->random(1)->first();
                }
            }
            return view('product.index', compact('product', 'shippingCountry', 'features', 'hasBuy', 'singleAds'));
        }
        abort(404);
    }
	
	protected function escapefile_url($url){
	  $parts = parse_url($url);
	  $path_parts = array_map('rawurldecode', explode('/', $parts['path']));

	  return
		$parts['scheme'] . '://' .
		$parts['host'] .
		implode('/', array_map('rawurlencode', $path_parts))
	  ;
	}

    public function singleimage($image)
    {
        $imagesUrl = $this->escapefile_url(asset('photos/story/'.$image));
        $exisImage = Storage::disk('local')->exists('photos/story/'.$image);
        if($exisImage){
            $images = './photos/story/'.$image;
            $exifImageType = mime_content_type($images);
            $imageType = explode('/', $exifImageType);
            if($imageType[0] == 'image' || $imageType[0] == 'video'){
				$fileSize = \File::size('photos/story/'.$image);
                $story = StoryItem::where('image', '=', $image)->first();
                $story->cview = $story->cview + 1;
                $story->save();
                header('Content-Type: $exifImageType');
				header("Content-length: $fileSize");
                echo file_get_contents($imagesUrl);
            } else {
                abort(404);
            }
        }
        abort(404);
    }

    public function adsimage($image)
    {
        $imagesUrl = $this->escapefile_url(asset('photos/ads/'.$image));
        $exisImage = Storage::disk('local')->exists('photos/ads/'.$image);
        if($exisImage){
            $images = './photos/ads/'.$image;
            $exifImageType = mime_content_type($images);
            $imageType = explode('/', $exifImageType);
            if($imageType[0] == 'image'){
                $ads = AdsSingle::where('dimage', '=', $image)->first();
                $ads->view = $ads->view + 1;
                $ads->save();
                header('Content-Type: $exifImageType');
                echo file_get_contents($imagesUrl);
            } else {
                abort(404);
            }
        }
        abort(404);
    }

    public function products()
    {
        if(check_permissions('manage_products')){
            $products = Product::orderBy('updated_at', 'DESC')->get();
            return view('admin.products.index', compact('products'));
        }
        abort(404);
    }

    public function editproducts($id)
    {
        if(check_permissions('manage_products')){
            $product = Product::find($id);
            $categories = Category::where('parent', '0')->get();
            $brands = Brand::all();
            $colorsList = Color::all();
            $variants = Variant::where('dshow', '1')->get();
            $shippingCountry = Country::where('shipping', '1')->get();
            $productBrand = count($product->brands) > 0 ? $product->brands[0]->id : '';
            $colors = [];
            if($product->colors){
                foreach(json_decode($product->colors) as $color){
                    array_push($colors, $color->id);
                }
            }
            $attributes = [];
            $allAttributes = [];
            if($product->attributes){
                $allAttributes = json_decode($product->attributes);
                foreach($allAttributes as $attribute){
                    array_push($attributes, $attribute->id);
                }
            }
            $tags = '';
            if(count($product->tags) > 0){
                foreach($product->tags as $tag){
                    $tags .= $tag->name.',';
                }
                $tags = rtrim($tags, ",");
            }
            if(!$product->shipping){
                // ray($product->shippings()->where('country_id', 2)->first());
            }
            return view('admin.products.edit', compact(
                'product', 'categories', 'variants', 'shippingCountry', 'brands', 'productBrand', 
                'tags', 'colors', 'allAttributes', 'attributes', 'colorsList'
            ));
        }
        abort(404);
    }

    public function storeproducts(Request $request, $id)
    {
        if(check_permissions('manage_products')){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'weight' => 'required',
                'size' => 'required',
                'price' => 'required',
                'stock' => 'required',
                'image' => 'required',
                'parentCategory' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'weight.required' => 'Pesha është i detyrueshëm',
                'size.required' => 'Madhësia e produktit është i detyrueshëm',
                'price.required' => 'Çmimi është i detyrueshëm',
                'stock.required' => 'Stoku është i detyrueshëm',
                'image.required' => 'Foto produkti është i detyrueshëm',
                'parentCategory.required' => 'Kategoritë janë të detyrueshme',
            ]);

            // dd($request);
            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->slug = null;
            $product->description = $request->description;
            $product->weight = $request->weight;
            $product->size = $request->size;
            if($request->activepersonalize == 'on'){
                $product->personalize = $request->personalizetitle;
            }
            // TODO: custom specification
            // Product Specification
            // $request->customfield_name;
            // $request->customfield_value;
            $product->price = $request->price;
            $product->sku = $request->sku;
            $product->stock = $request->stock;
            if($request->image){
                $product->image = $request->image;
            }
            if($request->gallery_image && count($request->gallery_image)){
                $deleteGallery = ProductGallery::where('product_id', $id)->delete();
                foreach($request->gallery_image as $gallery){
                    $productGallery = ProductGallery::updateOrCreate(
                        ['product_id' => $id, 'image'=> $gallery]
                    );
                }
            }
            if($request->customfield_name && count($request->customfield_name)){
                $deleteGallery = ProductSpecification::where('product_id', $id)->delete();
                $spNum = 0;
                foreach($request->customfield_name as $specification){
                    $productGallery = ProductSpecification::updateOrCreate(
                        ['product_id' => $id, 'name'=> $specification],
                        ['value' => $request->customfield_value[$spNum]]
                    );
                    $spNum++;
                }
            }
            
            if($request->subsubCategory){
                $prodCategory = $request->subsubCategory;
            } else if($request->subCategory) {
                $prodCategory = $request->subCategory;
            } else {
                $prodCategory = $request->parentCategory;
            }
            $product->category_id = $prodCategory;
            $tags = $request->get('tags');
            if (!empty($request->tags)) {
                $tagList = array_filter(explode(",", $tags));
                foreach ($tagList as $tags) {
                    $tag = Tag::firstOrCreate(['name' => $tags, 'slug' => Str::slug($tags)]);
                }
                $tags = Tag::whereIn('name', $tagList)->get()->pluck('id');
                $product->tags()->sync($tags);
            }
            $colorListArray = [];
            $variantListArray = [];
            if (!empty($request->variant_id)) {
                $varNum = 0;
                $deletedVariants = ProductVariant::where('product_id', $id)->update(['status' => 0]);
                foreach ($request->variant_id as $variants) {
                    $variantExplode = explode('-', $variants);
                    if(count($variantExplode)){
                        array_push($colorListArray, $variantExplode[0]);
                        $cvar = 0;
                        foreach($variantExplode as $variant){
                            $cvar++;
                            if($cvar == 1){
                                if(!in_array($variant, $colorListArray)){
                                    array_push($colorListArray, $variant);
                                }
                            } else {
                                if(!in_array($variant, $variantListArray)){
                                    array_push($variantListArray, $variant);
                                }
                            }
                        }
                    } else {
                        if(!in_array($variants, $colorListArray)){
                            array_push($colorListArray, $variants);
                        }
                    }
                    $productName = $request->variant_name[$varNum];
                    if($request->variant_price[$varNum]){
                        $productPrice = $request->variant_price[$varNum];
                    } else {
                        $productPrice = 0;
                    }
                    if($request->variant_qty[$varNum]){
                        $productStock = $request->variant_qty[$varNum];
                    } else {
                        $productStock = 0;
                    }
                    $productSku = $request->variant_sku[$varNum];
                    $productImage = $request->variant_img[$varNum];
                    $variant = ProductVariant::updateOrCreate(
                        ['product_id' => $id, 'slug'=> $variants],
                        ['name' => $productName, 'price' => $productPrice, 'sku'=> $productSku, 'stock' => $productStock, 'image' => $productImage, 'status'=>1]
                    );
                    $varNum++;
                }
				$deletedVariantsD = ProductVariant::where('product_id', $id)->where('status', '=', 0)->delete();
            } else {
                $deletedVariants = ProductVariant::where('product_id', $id)->get();
                if($deletedVariants->count()){
                    ProductVariant::where('product_id', $id)->delete();
                }
            }
            $colors = [];
            if($request->variant_colors){
                foreach($request->variant_colors as $color){
                    if(in_array($color, $colorListArray)){
                        $color = Color::find($color);
                        array_push($colors, array('id' => $color->id, 'name' => $color->name));
                    }
                }
            }
            $product->colors = json_encode($colors);
            $attributes = [];
            if($request->variant_attribute){
                foreach($request->variant_attribute as $attribute){
                    $attribute = Variant::find($attribute);
                    $options = $request->variant_attributes[$attribute->id];
                    $optionsAttribute = explode(', ', $options);
					$optionW = [];
                    foreach($optionsAttribute as $option){
                        array_push($optionW, trim($option));
                    }
                    $optionsAttributeN = [];
                    foreach($optionW as $attributeO){
                        if(in_array($attributeO, $variantListArray)){
                            array_push($optionsAttributeN, $attributeO);
                        }
                    }
                    if(count($optionsAttributeN)){
                        array_push($attributes, array('id' => $attribute->id, 'name' => $attribute->name, 'options' => $optionsAttributeN));
                    }
                }
            }
            $product->attributes = json_encode($attributes);
			if($request->vendor_shipping){
                $product->shipping = 1;
            } else {
                $product->shipping = 0;
            }
            $product->save();
            $product->brands()->sync($request->brands);
            if(!$request->vendor_shipping){
                if (!empty($request->shipping)) {
                    foreach ($request->shipping as $shipping) {
                        $prodShipping = 0;
                        if(isset($shipping['shipping']) && $shipping['shipping'] == 'on'){
                            $prodShipping = 1;
                        }
                        if($shipping['cost'] < 0){
                            $shipping['cost'] = 0;
                        }
                        $shippings = ProductShipping::updateOrCreate(
                            ['product_id' => $id, 'country_id'=> $shipping['country_id']],
                            ['shipping' => $prodShipping, 'free' => $shipping['free'], 'cost'=> $shipping['cost'], 'shipping_time' => $shipping['shipping_time']]
                        );
                    }
                }
            }
            session()->put('success','Ndryshimet për produktin u ruajtën me sukses.');
            return redirect()->route('admin.products.show');
        }
        abort(404);
    }

    public function deleteproducts($id)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id)){
            $product = Product::findorFail($id);
            $product->delete();
            ProductVariant::where('product_id', $id)->delete();
            ProductShipping::where('product_id', $id)->delete();
            session()->put('success','Produkti u fshi me sukses.');
            return redirect()->route('admin.products.show');
        }
        abort(404);
    }

    public function salesproducts($id)
    {
        if(check_permissions('manage_products') && is_numeric($id)){
            $product = Product::findorfail($id);
            return view('admin.products.sales', compact('product'));
        }
    }

    public function salesproductsUpdate(Request $request, $id)
    {
        if(check_permissions('manage_products') && is_numeric($id)){
            $product = Product::findorfail($id);
            if($product->psales){
                $sales = $product->psales;
            } else {
                $sales = new ProductSales();
                $sales->product_id = $product->id;
            }
            if(!$request->fsales){
                $fSales = 0;
            } else {
                $fSales = $request->fsales;
            }
            if($request->salesAction == 1){
                $sales->saction = 1;
                $sales->fsales = $fSales;
                $sales->save();
            } else {
                if($request->fsales > 1){
                    $sales->saction = 2;
                    $sales->fsales = $fSales;
                    $sales->save();
                }
            }
            session()->put('success','Shitjet Fallco të produktit u ruajtën me sukses.');
            return redirect()->route('admin.products.show');
        }
    }

    public function commentsproducts($id)
    {
        if(check_permissions('manage_products') && is_numeric($id)){
            $product = Product::findorfail($id);
            $ratings = $product->ratings;
            return view('admin.products.ratings.index', compact('product', 'ratings'));
        }
    }

    public function commentsproductsDelete($id, $sid)
    {
        if(check_permissions('manage_products') && check_permissions('delete_rights') && is_numeric($id) && is_numeric($sid)){
            $thisRating = ProductRating::findorfail($sid);
            if($thisRating->product_id == $id){
                $thisRating->delete();
                session()->put('success','Komenti u fshi me sukses.');
            return redirect()->route('admin.products.comments', $id);
            }
        }
    }

    public function vproducts()
    {
        if(check_permissions('manage_products') && vendor_status()){
            $vendor = current_vendor();
            $products = $vendor->products()->orderBy('updated_at', 'DESC')->get();
            return view('admin.products.vindex', compact('vendor', 'products'));
        }
        abort(404);
    }

    public function vnewproducts()
    {
        if(check_permissions('manage_products') && vendor_status()){
            $vendor = current_vendor();
            $categories = Category::where('parent', '0')->get();
            $brands = Brand::all();
            $colorsList = Color::all();
            $variants = Variant::where('dshow', '1')->get();
            $shippingCountry = Country::where('shipping', '1')->get();
            return view('admin.products.add', compact('vendor', 'categories', 'colorsList', 'variants', 'shippingCountry', 'brands'));
        }
        abort(404);
    }

    public function vsavenewproducts(Request $request)
    {
        // dd($request);
        if(check_permissions('manage_products') && vendor_status()){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'weight' => 'required',
                'size' => 'required',
                'price' => 'required',
                'stock' => 'required',
                'image' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'weight.required' => 'Pesha është i detyrueshëm',
                'size.required' => 'Madhësia e produktit është i detyrueshëm',
                'price.required' => 'Çmimi është i detyrueshëm',
                'stock.required' => 'Stoku është i detyrueshëm',
                'image.required' => 'Foto produkti është i detyrueshëm',
            ]);

            // dd($request);
            $product = new Product();
            $product->name = $request->name;
            $product->slug = null;
            $product->vendor_id = current_vendor()->id;
            $product->description = $request->description;
            $product->weight = $request->weight;
            $product->size = $request->size;
            if($request->activepersonalize == 'on'){
                $product->personalize = $request->personalizetitle;
            }
            // TODO: custom specification
            // Product Specification
            // $request->customfield_name;
            // $request->customfield_value;
            $product->price = $request->price;
            $product->sku = $request->sku;
            $product->stock = $request->stock;
            if($request->image){
                $product->image = $request->image;
            }
            if($request->subsubCategory){
                $prodCategory = $request->subsubCategory;
            } else if($request->subCategory) {
                $prodCategory = $request->subCategory;
            } else {
                $prodCategory = $request->parentCategory;
            }
            $product->category_id = $prodCategory;
            $colorListArray = [];
            $variantListArray = [];
            if (!empty($request->variant_id)) {
                foreach ($request->variant_id as $variants) {
                    $variantExplode = explode('-', $variants);
                    if(count($variantExplode)){
                        array_push($colorListArray, $variantExplode[0]);
                        $cvar = 0;
                        foreach($variantExplode as $variant){
                            $cvar++;
                            if($cvar == 1){
                                if(!in_array($variant, $colorListArray)){
                                    array_push($colorListArray, $variant);
                                }
                            } else {
                                if(!in_array($variant, $variantListArray)){
                                    array_push($variantListArray, $variant);
                                }
                            }
                        }
                    } else {
                        if(!in_array($variants, $colorListArray)){
                            array_push($colorListArray, $variants);
                        }
                    }
                }
            }
            $colors = [];
            if($request->variant_colors){
                foreach($request->variant_colors as $color){
                    if(in_array($color, $colorListArray)){
                        $color = Color::find($color);
                        array_push($colors, array('id' => $color->id, 'name' => $color->name));
                    }
                }
            }
            $product->colors = json_encode($colors);
            $attributes = [];
            if($request->variant_attribute){
                foreach($request->variant_attribute as $attribute){
                    $attribute = Variant::find($attribute);
                    $options = $request->variant_attributes[$attribute->id];
                    $optionsAttribute = explode(', ', $options);
					$optionW = [];
                    foreach($optionsAttribute as $option){
                        array_push($optionW, trim($option));
                    }
                    $optionsAttributeN = [];
                    foreach($optionW as $attributeO){
                        if(in_array($attributeO, $variantListArray)){
                            array_push($optionsAttributeN, $attributeO);
                        }
                    }
                    if(count($optionsAttributeN)){
                        array_push($attributes, array('id' => $attribute->id, 'name' => $attribute->name, 'options' => $optionsAttributeN));
                    }
                }
            }
            $product->attributes = json_encode($attributes);
			if($request->vendor_shipping){
                $product->shipping = 1;
            } else {
                $product->shipping = 0;
            }
            $product->save();
            $product->brands()->sync($request->brands);
            $tags = $request->get('tags');
            if (!empty($request->tags)) {
                $tagList = array_filter(explode(",", $tags));
                foreach ($tagList as $tags) {
                    $tag = Tag::firstOrCreate(['name' => $tags, 'slug' => Str::slug($tags)]);
                }
                $tags = Tag::whereIn('name', $tagList)->get()->pluck('id');
                $product->tags()->sync($tags);
            }
            if (!empty($request->variant_id)) {
                $varNum = 0;
                foreach ($request->variant_id as $variants) {
                    $productName = $request->variant_name[$varNum];
                    if($request->variant_price[$varNum]){
                        $productPrice = $request->variant_price[$varNum];
                    } else {
                        $productPrice = 0;
                    }
                    if($request->variant_qty[$varNum]){
                        $productStock = $request->variant_qty[$varNum];
                    } else {
                        $productStock = 0;
                    }
                    $productSku = $request->variant_sku[$varNum];
                    $productImage = $request->variant_img[$varNum];
                    $variant = ProductVariant::updateOrCreate(
                        ['product_id' => $product->id, 'slug'=> $variants],
                        ['name' => $productName, 'price' => $productPrice, 'sku'=> $productSku, 'stock' => $productStock, 'image' => $productImage]
                    );
                    $varNum++;
                }
            }
            if($request->gallery_image && count($request->gallery_image)){
                $deleteGallery = ProductGallery::where('product_id', $product->id)->delete();
                foreach($request->gallery_image as $gallery){
                    $productGallery = ProductGallery::updateOrCreate(
                        ['product_id' => $product->id, 'image'=> $gallery]
                    );
                }
            }
            if($request->customfield_name && count($request->customfield_name)){
                $deleteGallery = ProductSpecification::where('product_id', $product->id)->delete();
                $spNum = 0;
                foreach($request->customfield_name as $specification){
                    $productGallery = ProductSpecification::updateOrCreate(
                        ['product_id' => $product->id, 'name'=> $specification],
                        ['value' => $request->customfield_value[$spNum]]
                    );
                    $spNum++;
                }
            }
            if(!$request->vendor_shipping){
                if (!empty($request->shipping)) {
                    foreach ($request->shipping as $shipping) {
                        $prodShipping = 0;
                        if(isset($shipping['shipping']) && $shipping['shipping'] == 'on'){
                            $prodShipping = 1;
                        }
                        if($shipping['cost'] < 0){
                            $shipping['cost'] = 0;
                        }
                        $shippings = ProductShipping::updateOrCreate(
                            ['product_id' => $product->id, 'country_id'=> $shipping['country_id']],
                            ['shipping' => $prodShipping, 'free' => $shipping['free'], 'cost'=> $shipping['cost'], 'shipping_time' => $shipping['shipping_time']]
                        );
                    }
                }
            }
            session()->put('success','Produkti u shtua me sukses.');
            return redirect()->route('vendor.products.index');
        }
        abort(404);
    }

    public function veditproducts($id)
    {
        if(check_permissions('manage_products') && vendor_status()){
            $vendor = current_vendor();
            $product = Product::find($id);
            if($product->owner->id != $vendor->id){
                abort(404); 
            }
            $categories = Category::where('parent', '0')->get();
            $brands = Brand::all();
            $colorsList = Color::all();
            $variants = Variant::where('dshow', '1')->get();
            $shippingCountry = Country::where('shipping', '1')->get();
            $productBrand = count($product->brands) > 0 ? $product->brands[0]->id : '';
            $colors = [];
            if($product->colors){
                foreach(json_decode($product->colors) as $color){
                    array_push($colors, $color->id);
                }
            }
            $attributes = [];
            $allAttributes = [];
            if($product->attributes){
                $allAttributes = json_decode($product->attributes);
                foreach($allAttributes as $attribute){
                    array_push($attributes, $attribute->id);
                }
            }
            $tags = '';
            if(count($product->tags) > 0){
                foreach($product->tags as $tag){
                    $tags .= $tag->name.',';
                }
                $tags = rtrim($tags, ",");
            }
            if(!$product->shipping){
                // ray($product->shippings()->where('country_id', 2)->first());
            }
            return view('admin.products.vedit', compact(
                'vendor', 'product', 'categories', 'variants', 'shippingCountry', 'brands', 'productBrand', 
                'tags', 'colors', 'allAttributes', 'attributes', 'colorsList'
            ));
        }
        abort(404);
    }

    public function vstoreproducts(Request $request, $id)
    {
        if(check_permissions('manage_products') && vendor_status()){
            $validatedDate = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'weight' => 'required',
                'size' => 'required',
                'price' => 'required',
                'stock' => 'required',
                'image' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
                'description.required' => 'Përshkrimi është i detyrueshëm',
                'weight.required' => 'Pesha është i detyrueshëm',
                'size.required' => 'Madhësia e produktit është i detyrueshëm',
                'price.required' => 'Çmimi është i detyrueshëm',
                'stock.required' => 'Stoku është i detyrueshëm',
                'image.required' => 'Foto produkti është i detyrueshëm',
            ]);
            $product = Product::findOrFail($id);
            $product->name = $request->name;
            $product->slug = null;
            $product->description = $request->description;
            $product->weight = $request->weight;
            $product->size = $request->size;
            if($request->activepersonalize == 'on'){
                $product->personalize = $request->personalizetitle;
            }
            // TODO: custom specification
            // Product Specification
            // $request->customfield_name;
            // $request->customfield_value;
            $product->price = $request->price;
            $product->sku = $request->sku;
            $product->stock = $request->stock;
            if($request->image){
                $product->image = $request->image;
            }
            if($request->gallery_image && count($request->gallery_image)){
                $deleteGallery = ProductGallery::where('product_id', $id)->delete();
                foreach($request->gallery_image as $gallery){
                    $productGallery = ProductGallery::updateOrCreate(
                        ['product_id' => $id, 'image'=> $gallery]
                    );
                }
            }
            if($request->customfield_name && count($request->customfield_name)){
                $deleteGallery = ProductSpecification::where('product_id', $id)->delete();
                $spNum = 0;
                foreach($request->customfield_name as $specification){
                    $productGallery = ProductSpecification::updateOrCreate(
                        ['product_id' => $id, 'name'=> $specification],
                        ['value' => $request->customfield_value[$spNum]]
                    );
                    $spNum++;
                }
            }
            // $colors = [];
            // if($request->variant_colors){
            //     foreach($request->variant_colors as $color){
            //         $color = Color::find($color);
            //         array_push($colors, array('id' => $color->id, 'name' => $color->name));
            //     }
            // }
            // $product->colors = json_encode($colors);
            // $attributes = [];
            // if($request->variant_attribute){
            //     foreach($request->variant_attribute as $attribute){
            //         $attribute = Variant::find($attribute);
            //         $options = $request->variant_attributes[$attribute->id];
            //         $optionsAttribute = explode(',', $options);
            //         array_push($attributes, array('id' => $attribute->id, 'name' => $attribute->name, 'options' => $optionsAttribute));
            //     }
            // }
            // $product->attributes = json_encode($attributes);
            $product->brands()->sync($request->brands);
            if($request->subsubCategory){
                $prodCategory = $request->subsubCategory;
            } else if($request->subCategory) {
                $prodCategory = $request->subCategory;
            } else {
                $prodCategory = $request->parentCategory;
            }
            $product->category_id = $prodCategory;
            // if($prodCategory){
            //     $product->categories()->sync($prodCategory);
            // }
            // $product->categories()->sync($request->categories);
            $tags = $request->get('tags');
            if (!empty($request->tags)) {
                $tagList = array_filter(explode(",", $tags));
                foreach ($tagList as $tags) {
                    $tag = Tag::firstOrCreate(['name' => $tags, 'slug' => Str::slug($tags)]);
                }
                $tags = Tag::whereIn('name', $tagList)->get()->pluck('id');
                $product->tags()->sync($tags);
            }
            $colorListArray = [];
            $variantListArray = [];
            if (!empty($request->variant_id)) {
                $varNum = 0;
                $deletedVariants = ProductVariant::where('product_id', $id)->update(['status' => 0]);
                foreach ($request->variant_id as $variants) {
                    $variantExplode = explode('-', $variants);
                    if(count($variantExplode)){
                        array_push($colorListArray, $variantExplode[0]);
                        $cvar = 0;
                        foreach($variantExplode as $variant){
                            $cvar++;
                            if($cvar == 1){
                                if(!in_array($variant, $colorListArray)){
                                    array_push($colorListArray, $variant);
                                }
                            } else {
                                if(!in_array($variant, $variantListArray)){
                                    array_push($variantListArray, $variant);
                                }
                            }
                        }
                    } else {
                        if(!in_array($variants, $colorListArray)){
                            array_push($colorListArray, $variants);
                        }
                    }
                    $productName = $request->variant_name[$varNum];
                    if($request->variant_price[$varNum]){
                        $productPrice = $request->variant_price[$varNum];
                    } else {
                        $productPrice = 0;
                    }
                    if($request->variant_qty[$varNum]){
                        $productStock = $request->variant_qty[$varNum];
                    } else {
                        $productStock = 0;
                    }
                    $productSku = $request->variant_sku[$varNum];
                    $productImage = $request->variant_img[$varNum];
                    $variant = ProductVariant::updateOrCreate(
                        ['product_id' => $id, 'slug'=> $variants],
                        ['name' => $productName, 'price' => $productPrice, 'sku'=> $productSku, 'stock' => $productStock, 'image' => $productImage, 'status'=>1]
                    );
                    $varNum++;
                }
                $deletedVariantsD = ProductVariant::where('product_id', $id)->where('status', '=', 0)->delete();
            } else {
                $deletedVariants = ProductVariant::where('product_id', $id)->get();
                if($deletedVariants->count()){
                    ProductVariant::where('product_id', $id)->delete();
                }
            }
            $colors = [];
            if($request->variant_colors){
                foreach($request->variant_colors as $color){
                    if(in_array($color, $colorListArray)){
                        $color = Color::find($color);
                        array_push($colors, array('id' => $color->id, 'name' => $color->name));
                    }
                }
            }
            $product->colors = json_encode($colors);
            $attributes = [];
            if($request->variant_attribute){
                foreach($request->variant_attribute as $attribute){
                    $attribute = Variant::find($attribute);
                    $options = $request->variant_attributes[$attribute->id];
                    $optionsAttribute = explode(',', $options);
					$optionW = [];
                    foreach($optionsAttribute as $option){
                        array_push($optionW, trim($option));
                    }
                    $optionsAttributeN = [];
                    foreach($optionW as $attributeO){
                        if(in_array($attributeO, $variantListArray)){
                            array_push($optionsAttributeN, $attributeO);
                        }
                    }
                    if(count($optionsAttributeN)){
                        array_push($attributes, array('id' => $attribute->id, 'name' => $attribute->name, 'options' => $optionsAttributeN));
                    }
                }
            }
            $product->attributes = json_encode($attributes);
            if(!$request->vendor_shipping){
                if (!empty($request->shipping)) {
                    foreach ($request->shipping as $shipping) {
                        $prodShipping = 0;
                        if(isset($shipping['shipping']) && $shipping['shipping'] == 'on'){
                            $prodShipping = 1;
                        }
                        if($shipping['cost'] < 0){
                            $shipping['cost'] = 0;
                        }
                        $shippings = ProductShipping::updateOrCreate(
                            ['product_id' => $id, 'country_id'=> $shipping['country_id']],
                            ['shipping' => $prodShipping, 'free' => $shipping['free'], 'cost'=> $shipping['cost'], 'shipping_time' => $shipping['shipping_time']]
                        );
                    }
                }
            }
			if($request->vendor_shipping){
                $product->shipping = 1;
            } else {
                $product->shipping = 0;
            }
            $product->save();
            session()->put('success','Ndryshimet për produktin u ruajtën me sukses.');
            return redirect()->route('vendor.products.index');
        }
        abort(404);
    }

    public function vdeleteproducts($id)
    {
        $product = Product::findorFail($id);
        if(check_permissions('manage_products') && check_permissions('delete_rights') && vendor_status() && $product->owner->id == current_vendor()->id){
            $product->delete();
            ProductVariant::where('product_id', $id)->delete();
            ProductShipping::where('product_id', $id)->delete();
            session()->put('success','Produkti u fshi me sukses.');
            return redirect()->route('vendor.products.index');
        }
        abort(404); 
    }

    public function addComment(Request $request, $id)
    {
        if(is_numeric($id)){
            $validatedDate = $request->validate([
                'product_rating' => 'required',
            ], [
                'product_rating.required' => 'Vlerso Produktin me yje sipas mendimit tuaj',
            ]);
            
            $review = new ProductRating();
            $review->product_id = $id;
            $review->user_id = current_user()->id;
            $review->rating = $request->product_rating;
            $review->comment = $request->comment;
            $review->save();
            session()->put('success','Vlerësimi juaj u shtua me sukses.');
            return redirect()->back();
        }
        abort(404);
    }

    public function reports()
    {
        $reports = ProductReports::all();
        return view('admin.products.reports.index', compact('reports'));
    }

    public function reportsView($id)
    {
        if(is_numeric($id)){
            $report = ProductReports::findOrFail($id);

            return view('admin.products.reports.view', compact('report'));
        }
        abort(404);
    }

    public function reportsDelete($id)
    {
        if(is_numeric($id)){
            $reports = ProductReports::findOrFail($id);
            $reports->delete();
            session()->put('success','Raportimi u fshi me sukses.');
            return redirect()->back();
        }
        abort(404);
    }
}
