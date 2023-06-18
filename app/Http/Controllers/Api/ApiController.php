<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Story;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\TransportInfo;
use App\Models\MobileSlider;
use App\Models\Pages;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\OrderFResource;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\OfferProductResoruce;
use App\Http\Resources\VariantResource;
use App\Http\Resources\ProductsResource;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderVendorResource;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\OrderVendorFResource;
use App\Http\Resources\CategorySliderResource;
use App\Http\Resources\ProductPaginationResource;
use App\Http\Resources\StoriesResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\SVendorResource;
use App\Http\Resources\OpenHourResource;
use App\Models\StoryItem;
use Illuminate\Support\Str;

class ApiController extends BaseController
{
    public function config()
    {
        $config = [];
        // $config['package'] = "com.codeit.dosja";
        // $config['apple_id'] = "1460706531";
        $config['request_timeout'] = 30000;
        $config['root_endpoint'] = "https://new57.elefandi.com/mjson/";
        // $config['share_android'] = "https://play.google.com/store/apps/details?id=com.codeit.dosja";
        // $config['share_ios'] = "https://itunes.apple.com/us/app/dosja/id1460706531?mt=8";
        $config['onesignal_app_id'] = "c03ddfd6-f0d2-49b1-b99a-b1c7356605b1";
        $config['last_time'] = 1630940899;
        return $config;
    }

    public function homeSlider()
    {
        $mSLiders = MobileSlider::all();
        // $slider = [];
        // $slider[1]['image'] = 'https://cdn.elefandi.com/ele556425.jpg';
        // $slider[1]['link'] = '';
        // $slider[2]['image'] = 'https://cdn.elefandi.com/hk987654.jpg';
        // $slider[2]['link'] = '';
        // $slider[3]['image'] = 'https://cdn.elefandi.com/kh789456.jpg';
        // $slider[3]['link'] = '';
        // return collect($slider)->values()->toArray();
        // return $this->sendResponse(collect($mSLiders)->values()->toArray(), 'Product deleted successfully.');
        return SliderResource::collection($mSLiders);
    }

    public function allCategories()
    {
        if (Cache::has('pcat')) {
            $categories = Cache::get('pcat');
        } else {
            $categories = Cache::rememberForever('pcat', function () {
                return Category::where('parent', '0')->get();
            });
        }
        $categorisArr = [];
        $catNum = 0;
        foreach($categories as $category){
            $catNum++;
            $categoryId = $category->id;
            $products = [];
            $categoryChildren = [];
            $categoryChildren = $category->children;

            if (Cache::has('cat'.$categoryId)) {
                $baseCategoryIds = Cache::get('cat'.$categoryId);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$categoryId, function() use ($categoryId) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $categoryId)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(8);
            $products = new ProductPaginationResource($productsS);
            
            $categorisArr[] = [
                "id" => $categoryId,
                "name" => $category->name,
                "icon" => $category->icon,
                "image" => $category->image,
                "subcat" => $categoryChildren,
                "products" => $products,
                "slider" => CategorySliderResource::collection($category->sliders),
                "catNum" => $catNum
            ];
        }
        return $categorisArr;
    }

    public function allCategoriesSliders($id)
    {
        $category = Category::find($id);
        $categoryId = $category->id;
        if (Cache::has('cat'.$categoryId)) {
            $baseCategoryIds = Cache::get('cat'.$categoryId);
        } else {
            $baseCategoryIds = Cache::rememberForever('cat'.$categoryId, function() use ($categoryId) {
                $data = Category::with(['products', 'childrenRecursive'])->where('id', $categoryId)->get()->toArray();
                return Arr::pluck($this->flatten($data), 'id');
            });
        }
        $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(8);
        $products = new ProductPaginationResource($productsS);
        
        $categorisArr = [
            "id" => $categoryId,
            "name" => $category->name,
            "icon" => $category->icon,
            "image" => $category->image,
            "subcat" => $category->children,
            "products" => $products,
            "slider" => CategorySliderResource::collection($category->sliders),
        ];
        return $categorisArr;
        // if (Cache::has('pcat')) {
        //     $categories = Cache::get('pcat');
        // } else {
        //     $categories = Cache::rememberForever('pcat', function () {
        //         return Category::where('parent', '0')->get();
        //     });
        // }
        // $categorisArr = [];
        // $catNum = 0;
        // foreach($categories as $category){
        //     $catNum++;
        //     $categoryId = $category->id;
        //     if (Cache::has('cat'.$categoryId)) {
        //         $baseCategoryIds = Cache::get('cat'.$categoryId);
        //     } else {
        //         $baseCategoryIds = Cache::rememberForever('cat'.$categoryId, function() use ($categoryId) {
        //             $data = Category::with(['products', 'childrenRecursive'])->where('id', $categoryId)->get()->toArray();
        //             return Arr::pluck($this->flatten($data), 'id');
        //         });
        //     }
        //     $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->paginate(10);
        //     $products = new ProductPaginationResource($productsS);
            
        //     $categorisArr[] = [
        //         "id" => $categoryId,
        //         "name" => $category->name,
        //         "icon" => $category->icon,
        //         "image" => $category->image,
        //         "subcat" => $category->children,
        //         "products" => $products,
        //         "slider" => CategorySliderResource::collection($category->sliders),
        //         "catNum" => $catNum
        //     ];
        // }
        // return $categorisArr;
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

    public function homeCategories()
    {
        if (Cache::has('pcat')) {
            $categories = Cache::get('pcat');
        } else {
            $categories = Cache::rememberForever('pcat', function () {
                return Category::where('parent', '0')->get();
            });
        }
        $homeCategories = $categories->where('home', '=', '1');
        $allCategories = [];
        foreach($homeCategories as $category){
            $categoryId = $category['id'];
            if (Cache::has('cat'.$categoryId)) {
                $baseCategoryIds = Cache::get('cat'.$categoryId);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$categoryId, function() use ($categoryId) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $categoryId)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->inRandomOrder()->take(7)->get();
            // $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->random(7)->get();
            $products = ProductResource::collection($productsS);
            $allCategories[] = [
                'id' => $categoryId,
                'name' => $category['name'],
                'description' => $category['description'],
                'image' => $category['image'],
                'icon' => $category['icon'],
                'products' => $products
            ];
        }
        return (array) $allCategories;
    }

    public function homeCategoriesProduct()
    {
        $allProducts = [];
        foreach($this->homeCategories() as $category){
            ray($category);
            $categoryId = $category['id'];
            if (Cache::has('cat'.$categoryId)) {
                $baseCategoryIds = Cache::get('cat'.$categoryId);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$categoryId, function() use ($categoryId) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $categoryId)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $products = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->take(9)->get();
            $allProducts[] = $products;
        }
    }

    public function singleProduct($id)
    {
        if(is_numeric($id)){
            $product = Product::findorfail($id);
            if($product && $product->status == 1 && $product->vstatus == 1){

                $shippingCountry = Country::where('shipping', '1')->get();
                if($product->minoffers() && $product->minoffers()->discount != 0){
                    $offer = $product->minoffers();
                    if($offer->type < 3){
                        $offerPrice = round($product->price - (($product->price * $offer->discount)/100), 2);
                        $offerDiscount = '-'.round($offer->discount, 1).'%';
                    } else {
                        $offerPrice = $offer->discount;
                        $offerDiscount = '-'.($product->price - $offer->discount).'€';
                    }
                    $offerExpire = \Carbon\Carbon::parse($offer->main->expire_date)->format('U');
                    $offerDetail = [
                        'offer' => true,
                        'cost' => $product->price,
                        'nprice' => $offerPrice,
                        'discount' => $offerDiscount,
                        'expire' => $offerExpire.'000',
                    ];
                } else {
                    $offerDetail = [
                        'offer' => false,
                        'cost' => $product->price,
                        'nprice' => 0,
                        'discount' => 0,
                        'expire' => false,
                    ];
                }
                // $productsS = $product->owner->products()->where('id', '!=', $product->id)->where([['status', 1], ['vstatus', 1]])->take(10)->get();
                // $products = ProductResource::collection($productsS);
                $variantsS = $product->variants;
                $variants = VariantResource::collection($variantsS);
                $selVariant = 0;
                if(count($variants)){
                    $selVariant = $variants[0];
                }
                $ii = 0;
                $productShippings = [];
                foreach($shippingCountry as $country){
                    $ii++;
                    $getShippingVendor = $product->owner->shippings()->where('country_id', '=', $country->id)->first();
                    $getShipping = $product->shippings->where('country_id', '=', $country->id)->first();
                    if(!$product->shipping && $getShipping){
                        $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                        $productShippings[$country->id] = [
                            "id" => $getShipping->id,
                            "country" => $country->id,
                            "country_name" => $country->name,
                            "shipping" => $getShipping->shipping,
                            "free" => $getShipping->free,
                            "cost" => $getShipping->cost,
                            "shipping_time" => $getShipping->shipping_time,
                            "timeName" => $transportInfo->name,
                        ];
                    } else {
                        if($getShippingVendor){
                            $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                            // array_push($productShippings, $getShippingVendor);
                            $productShippings[$country->id] = [
                                "id" => $getShippingVendor->id,
                                "country" => $country->id,
                                "country_name" => $country->name,
                                "shipping" => $getShippingVendor->transport,
                                "free" => ($getShippingVendor->cost) ? 0: 1,
                                "cost" => ($getShippingVendor->cost) ?: 0,
                                "shipping_time" => $getShippingVendor->transtime,
                                "timeName" => (($transportInfo) ? $transportInfo->name : 'Ska Transport' ),
                            ];
                        }
                    }
                    if($getShippingVendor){
                        if($getShippingVendor->limit){
                            $shippingLimit = $getShippingVendor->limit;
                            $shippingCost = $getShippingVendor->cost;
                        } else {
                            $shippingLimit = '';
                            $shippingCost = false;
                        }
                        $shippingTrans = $getShippingVendor->transport;
                        if($shippingTrans == 2  && !$shippingLimit){
                            $shippingTrans = 1;
                        }
                        if($shippingTrans == 4 && !$shippingLimit){
                            $shippingTrans = 3;
                        }
                        $productShippings[$country->id]['vendor'] = [
                            'limit' => $shippingLimit,
                            'cost' => $shippingCost,
                            'shippingTrans' => $shippingTrans
                        ];
                    }
                    // if($ii < 3){
                    //     $productShippings .= ',';
                    // }
                }
                $personalizeTitle = NULL;
                if($product->personalize && $product->personalize != 'null'){
                    $personalizeTitle = $product->personalize;
                }
                $svendor = new SVendorResource($product->owner);
                return [
                    "id" => $product->id,
                    "name" => $product->name,
                    "slug" => route('single.product', [$product->owner->slug, $product->id]),
                    "sku" => $product->sku,
                    "description" => $product->description,
                    "vendor_id" => $product->vendor_id,
                    "category_id" => $product->category_id,
                    "image" => ((\File::exists('photos/products/'.$product->image)) ? asset('photos/products/'.$product->image) : 'assets/images/no-image-210.png'),
                    "weight" => $product->weight,
                    "size" => $product->size,
                    "personalize" => $personalizeTitle,
                    "price" => $offerDetail,
                    "stock" => $product->stock,
                    "colors" => $product->colors,
                    "attributes" => $product->attributes,
                    "variants" => $variants,
                    "selvariants" => $selVariant,
                    "shipping" => $productShippings,
                    "gallery" => GalleryResource::collection($product->gallery),
                    "vendor" => $svendor,
                    "specification" => $product->specification,
                ];
            }
        }
    }

    public function singleProductsVendor($id)
    {
        if(is_numeric($id)){
            $product = Product::findorfail($id);
            $productsS = $product->owner->products()->where('id', '!=', $product->id)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->take(10)->get();
            $products = ProductResource::collection($productsS);
            return $products;
        }
    }

    public function singleProductSimilar($id)
    {
        if(is_numeric($id)){
            $product = Product::findorfail($id);
            // $productsS = $product->owner->products()->where('id', '!=', $product->id)->take(10)->get();
            $productsS = $product->similarProducts();
            $products = ProductResource::collection($productsS);
            return $products;
        }
    }

    public function allProducts($page=1, $cat=false)
    {
        if($cat){
            if (Cache::has('cat'.$cat)) {
                $baseCategoryIds = Cache::get('cat'.$cat);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$cat, function() use ($cat) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $cat)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(8);
            $products = new ProductPaginationResource($productsS);
        } else {
            $productsS = Product::where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(10);
            $products = new ProductPaginationResource($productsS);
        }
        return $products;
    }

    public function searchProducts(Request $request, $cat=0, $page=1)
    {
        if($request->s){
            $search = htmlspecialchars($request->s);
            if($search){
                if($cat){
                    return 'test';
                } else {
                    $productsS = Product::where(
                        function ($query) use ($search) {
                            $query->where('name', 'LIKE', '%'.$search.'%')->orWhere('description', 'LIKE', '%'.$search.'%');
                        }
                    )->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->get();
                    
                    $products = ProductResource::collection($productsS);
                    return (array) $products->values()->toArray();
                }
            }
            return [];
        }
    }

    public function getCountriesList()
    {
        $countries = Country::all();
        return $countries->values()->toArray();
    }
    
    public function getshCountriesList()
    {
        $countries = Country::where('shipping', '=', 1)->get();
        return $countries->values()->toArray();
    }

    public function getCitiesList($country)
    {
        $cities = City::where('country_id', '=', $country)->where('status', '=', 1)->get();
        return $cities->values()->toArray();
    }

    public function loginUser(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $fields['email'])->where('status', '=', 1)->first();
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }
        // $user = User::where('email', 'test@codeit.al')->first();
        
        // $token = $user->generateToken();
        $token = $user->createToken('app-elefandi-v1');
        // dd($token);
        $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];
        if($request->onesignal_id){
            $user->onesignal()->updateOrCreate(
                ['user_id' => $user->id],
                ['onesignal' => $request->onesignal_id]
            );
        }
        if($user->vendor() && $user->vendor()->count()){
			$response['vendor_name'] = $user->vendor()->name;
            $response['vendor'] = $user->vendor()->id;
			$response['uvid'] = $user->vendor()->uvid;
			$vendPermission = $user->vroles->first();
			if($vendPermission){
				$response['vpermission'] = [];
				$response['vpermission']['manage_vendor'] = (isset($vendPermission['manage_vendor']) && $vendPermission['manage_vendor'])?1:0;
				$response['vpermission']['manage_products'] = (isset($vendPermission['manage_products']) && $vendPermission['manage_products'])?1:0;
				$response['vpermission']['manage_orders'] = (isset($vendPermission['manage_orders']) && $vendPermission['manage_orders'])?1:0;
				$response['vpermission']['manage_chat'] = (isset($vendPermission['manage_chat']) && $vendPermission['manage_chat'])?1:0;
				$response['vpermission']['manage_supports'] = (isset($vendPermission['manage_supports']) && $vendPermission['manage_supports'])?1:0;
				$response['vpermission']['manage_offers'] = (isset($vendPermission['manage_offers']) && $vendPermission['manage_offers'])?1:0;
				$response['vpermission']['manage_stories'] = (isset($vendPermission['manage_stories']) && $vendPermission['manage_stories'])?1:0;
				$response['vpermission']['manage_ads'] = (isset($vendPermission['manage_ads']) && $vendPermission['manage_ads'])?1:0;
				$response['vpermission']['manage_notifications'] = (isset($vendPermission['manage_notifications']) && $vendPermission['manage_notifications'])?1:0;
				$response['vpermission']['delete_rights'] = (isset($vendPermission['delete_rights']) && $vendPermission['delete_rights'])?1:0;
				$response['vpermission']['can_edit'] = (isset($vendPermission['can_edit']) && $vendPermission['can_edit'])?1:0;
			}
        }

        return response($response, 201);
    }

    public function logoutUser()
    {
        if(current_user()){
            current_user()->onesignal()->delete();
        }
        return true;
    }

    public function tokenValidate(Request $request)
    {
        // dd($request);
        //return 'tes';
		//$response = ['validate'=>1];
		//return response($response, 200);
		return response([
            'message' => 'Authenticated',
            'status' => 200
        ]);
    }

    public function getWishList(Request $request)
    {
        $productIds = json_decode($request->wishlist);
        $flattened = Arr::flatten($productIds);
        $productsS = Product::whereIn('id', $flattened)->where([['status', 1], ['vstatus', 1]])->get();
        $products = ProductResource::collection($productsS);
        return $products->values()->toArray();
    }

    public function getCarts(Request $request)
    {
        $productIds = json_decode($request->cart);
        $countryId = json_decode($request->country);
        $productCarts = [];
        foreach ($productIds as $key=>$value){
            $products = [];
            $vendorId = substr($key, 1);
            $dVendor = Vendor::find($vendorId);
            $subTotal = 0;
            $transport = 0;
            $hasOffer= false;
            foreach($value as $product){
                $dproduct = Product::find($product->id);
                if($dproduct && $dproduct->status==1 && $dproduct->vstatus==1){
                    if($product->variant != 0){
                        $currentVariant = $dproduct->cartVariant($product->variant);
                        $prodPrice = $currentVariant->price ? $currentVariant->price : $dproduct->price;
                        $oldPrice = $prodPrice;
                        if($dproduct->offers($product->variant)){
                            $offer = $dproduct->offers($product->variant);
                            if($offer->type < 3){
                                $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                                $offerDiscount = '-'.round($offer->discount, 1).'%';
                            } else {
                                $offerPrice = $offer->discount;
                                $offerDiscount = '-'.($prodPrice - $offer->discount).'€';
                            }
                            $hasOffer= true;
                        } else {
                            $offer = '';
                        }
                        $prodMaxStock = $currentVariant->stock ? $currentVariant->stock : $dproduct->stock;
                        $productStock = $product->qty;
                        if($prodMaxStock){
                            if($product->qty > $prodMaxStock){
                                $productStock = $prodMaxStock;
                            }
                        } else {
                            $noStock = true;
                        }
                        $varName = $currentVariant->name;
                    } else {
                        $prodPrice = $dproduct->price;
                        $oldPrice = $prodPrice;
                        if($dproduct->offers()){
                            $offer = $dproduct->offers();
                            if($offer->type < 3){
                                $offerPrice = round($prodPrice - (($prodPrice * $offer->discount)/100), 2);
                                $offerDiscount = '-'.round($offer->discount, 1).'%';
                            } else {
                                $offerPrice = $offer->discount;
                                $offerDiscount = '-'.($prodPrice - $offer->discount).'€';
                            }
                            $hasOffer= true;
                        } else {
                            $offer = '';
                        }
                        $prodMaxStock = $dproduct->stock;
                        $productStock = $product->qty;
                        if($prodMaxStock){
                            if($product->qty > $prodMaxStock){
                                $productStock = $prodMaxStock;
                            }
                        } else {
                            $productStock = 0;
                            $prodMaxStock = 0;
                            $noStock = true;
                        }
                        $varName = '';
                    }
                    if($hasOffer){
                        if($prodPrice - $offer->discount){
                            $offerDetail = [
                                'offer' => true,
                                'cost' => $oldPrice,
                                'nprice' => $offerPrice,
                                'discount' => $offerDiscount,
                            ];
                        } else {
                            $offerDetail = [
                                'offer' => false,
                                'cost' => $oldPrice,
                                'nprice' => 0,
                                'discount' => 0,
                            ];
                        }
                    } else {
                        $offerDetail = [
                            'offer' => false,
                            'cost' => $oldPrice,
                            'nprice' => 0,
                            'discount' => 0,
                        ];
                    }
                    $getShipping = $dproduct->shippings->where('country_id', '=', $countryId)->first();
                    if(!$dproduct->shipping && $getShipping){
                        $transportInfo = TransportInfo::where('id', '=', $getShipping->shipping_time)->first();
                        $productShippings = [
                            "id" => $getShipping->id,
                            "country" => $countryId,
                            "shipping" => $getShipping->shipping,
                            "free" => $getShipping->free,
                            "cost" => $getShipping->cost,
                            "shipping_time" => $getShipping->shipping_time,
                            "timeName" => $transportInfo->name,
                        ];
                    } else {
                        $getShippingVendor = $dproduct->owner->shippings()->where('country_id', '=', $countryId)->first();
                        if($getShippingVendor){
                            $transportInfo = TransportInfo::where('id', '=', $getShippingVendor->transtime)->first();
                            $productShippings = [
                                "id" => $getShippingVendor->id,
                                "country" => $countryId,
                                "shipping" => $getShippingVendor->transport,
                                "free" => ($getShippingVendor->cost) ? 0: 1,
                                "cost" => ($getShippingVendor->cost) ?: 0,
                                "shipping_time" => $getShippingVendor->transtime,
                                "timeName" => (($transportInfo) ? $transportInfo->name : 'Ska Transport' ),
                            ];
                        }
                    }
                    array_push($products, [
                        'id' => $dproduct->id,
                        'name' => $dproduct->name,
                        'image' => asset('/photos/products/'.$dproduct->image),
                        'cost' => $dproduct->price,
                        'offer' => $dproduct->price,
                        'personalize' => $dproduct->personalize,
                        'stock' => $dproduct->stock,
                        'qty' => $product->qty,
                        'available' => 1,
                        'price' => $offerDetail,
                        'variant' => $varName,
                        'shipping' => $productShippings,
                    ]);
                    $subTotal += $dproduct->price * $product->qty;
                    $transport += 0;
                }
            }
            array_push($productCarts, [
                'id' => $dVendor->id,
                'vendCost' => 0,
                'image' => asset('/photos/vendor/'.$dVendor->logo_path),
                'name' => $dVendor->name,
                'vendType' => '',
                'verified' => 1,
                'products' => $products,
                'subTotal' => $subTotal,
                'transport' => $transport,
            ]);
        }
        return $productCarts;
        // $flattened = Arr::flatten($productIds);
        // $products = Product::whereIn('id', $flattened)->get();
        // return $products->values()->toArray();
    }

    public function getVendor()
    {
        if(current_vendor()){
            $thisMonth = date('Y-06-01 00:00:05');
            $ordersMonthSum = current_vendor()->orders->where('created_at', '>', $thisMonth)->sum('value');
            return $ordersMonthSum;
        }
    }

    public function getfVendor()
    {
        if(current_vendor()){
            $today = date('Y-m-d 00:00:05');
            $thisMonth = date('Y-06-01 00:00:05');
            $ordersTodaySum = current_vendor()->orders->where('created_at', '>', $today)->sum('value');
            $ordersMonthSum = current_vendor()->orders->where('created_at', '>', $thisMonth)->sum('value');
            return [
                'sales_d' => ''.$ordersTodaySum,
                'sales_m' => ''.$ordersMonthSum,
                'name' => current_vendor()->name,
                'location' => current_vendor()->cities->name.', '.current_vendor()->country()->name,
                'rights' => [
                    'vendor' => ((mcheck_permissions('manage_vendor')) ? true : false),
                    'products' => ((mcheck_permissions('manage_products')) ? true : false),
                    'orders' => ((mcheck_permissions('manage_orders')) ? true : false),
                    'chat' => ((mcheck_permissions('manage_chat')) ? true : false),
                    'support' => ((mcheck_permissions('manage_supports')) ? true : false),
                    'stories' => ((mcheck_permissions('manage_stories')) ? true : false),
                    'offers' => ((mcheck_permissions('manage_offers')) ? true : false),
                    'delete' => ((mcheck_permissions('delete_rights')) ? true : false),
                ]
            ];
        }
    }

    public function getProducts()
    {
        if(current_vendor()){
            $products = current_vendor()->products()->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'DESC')->get();
            return ProductsResource::collection($products);
        }
    }

    public function getVendorInfo($id)
    {
        if(is_numeric($id)){
            $currVendor = Vendor::findOrFail($id);
            if($currVendor ){

                $categoriesArr = [];
                $categories = $currVendor->products()->with('category')->get()->pluck('category');
                foreach($categories as $category) {
                    if($category){
                        if($category->parent == 0){
                            if(!isset($categoriesArr[$category->id])){
                                $categoriesArr[$category->id] = [];
                            }
                            $categoriesArr[$category->id]['info'] = $category;
                        } else {
                            $currCategory = $category->parents;
                            if($currCategory->parent == 0){
                                if(!isset($categoriesArr[$currCategory->id])){
                                    $categoriesArr[$currCategory->id] = [];
                                }
                                $categoriesArr[$currCategory->id]['info'] = $currCategory;
                                if(!isset($categoriesArr[$currCategory->id]['category'])){
                                    $categoriesArr[$currCategory->id]['category'] = [];
                                }
                                if(!isset($categoriesArr[$currCategory->id]['category'][$category->id])){
                                    $categoriesArr[$currCategory->id]['category'][$category->id] = [];
                                }
                                $categoriesArr[$currCategory->id]['category'][$category->id]['info'] = $category;
                            } else {
                                $subCategory = $currCategory->parents;
                                if(!isset($categoriesArr[$subCategory->id])){
                                    $categoriesArr[$subCategory->id] = [];
                                }
                                $categoriesArr[$subCategory->id]['info'] = $subCategory;
                                if(!isset($categoriesArr[$subCategory->id]['category'])){
                                    $categoriesArr[$subCategory->id]['category'] = [];
                                }
                                if(!isset($categoriesArr[$subCategory->id]['category'][$currCategory->id])){
                                    $categoriesArr[$subCategory->id]['category'][$currCategory->id] = [];
                                }
                                $categoriesArr[$subCategory->id]['category'][$currCategory->id]['info'] = $currCategory;
                                $categoriesArr[$subCategory->id]['category'][$currCategory->id]['category'][] = $category;
    
                            }
                        }
                    }
                }
                $productsS = $currVendor->products()->where([['status', 1], ['vstatus', 1]])->paginate(10);
                $products = ProductResource::collection($productsS);

                $shipingCountryList = [];
                $shippingCountry = Country::where('shipping', '1')->get();
                foreach($shippingCountry as $country){
                    $vendorShipping = $currVendor->shippings()->where('country_id', '=', $country->id)->first();
                    if($vendorShipping && $vendorShipping->transport ){
                        if($vendorShipping->transport == 2){
                            $vendorCInfo = ' ofron shërbimin blej dy ose më shumë produkte dhe paguaj tranportin vetëm për njërin produkt';
                        } else if($vendorShipping->transport == 3 && $vendorShipping->limit) {
                            $vendorCInfo = ' ofron transport falas mbi '.$vendorShipping->limit.'€';
                        } else if($vendorShipping->transport == 4 && $vendorShipping->limit) {
                            $vendorCInfo = ' ofron transport falas mbi '.$vendorShipping->limit.'€ dhe <br> ofron shërbimin blej dy ose më shumë produkte dhe paguaj tranportin vetëm për njërin produkt';
                        } else {
                            $vendorCInfo = ' ofron tranport normal për secilin produkt';
                        }
                        $shipingCountryList[] = [
                            'country' => $country->name,
                            'shipping' => $vendorCInfo,
                        ];
                    }
                }

                $ratingsAll = $currVendor->products()->whereHas('ratings')->select('id')->with('ratings:rating,product_id')->get();
                $ratingAverageF = 0;
                if($ratingsAll){
                    $ratingAverage = $ratingsAll->pluck('ratings')->flatten()->pluck('rating')->avg();
                    if(Str::length($ratingAverage) == 1){
                        $ratingAverageF = $ratingAverage.'.0';
                    } else {
                        $ratingAverageRound = round($ratingAverage, 2);
                        $ratingAverageF = $ratingAverageRound;
                    }
                }

                return (array) [
                    'vendor' => [
                        'name' => $currVendor->name,
                        'description' => $currVendor->description,
                        'city' => ((is_numeric($currVendor->city)) ? $currVendor->cities->name : $currVendor->city),
                        'country' => $currVendor->country()->name,
                        'address' => $currVendor->address,
                        'zipcode' => $currVendor->zipcode,
                        'email' => $currVendor->email,
                        'phone' => $currVendor->phone,
                        'logo' => (\File::exists('photos/vendor/'.$currVendor->logo_path)) ? asset('photos/vendor/'.$currVendor->logo_path) : 'assets/images/no-image-210.png',
                        'cover' => (\File::exists('photos/cover/'.$currVendor->cover_path)) ? asset('photos/cover/'.$currVendor->cover_path) : 'assets/images/vendor-cover.jpg',
                        'status' => $currVendor->vstatus,
                        'verified' => $currVendor->verified,
                        'openhour' => new OpenHourResource($currVendor->workhour),
                        'created_at' => Carbon::parse($currVendor->created_at)->format('d.m.Y H:i'),
                        'shipping' => $shipingCountryList,
                        'rating' => $ratingAverageF,
                    ],
                    'categories' => (array) collect($categoriesArr)->values()->toArray(),
                    'informations' => '',
                    'products' => $products,
                ];
            }
        }
    }

    public function getVendorProducts($oid, $id)
    {
        if(is_numeric($id) && is_numeric($oid)){
            $currVendor = Vendor::findOrFail($id);
            if($oid == 1){
                $productsS = $currVendor->products()->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'DESC')->paginate(10);
            } else if($oid == 2){
                $productsS = $currVendor->products()->where([['status', 1], ['vstatus', 1]])->orderBy('sales', 'DESC')->paginate(10);
            } else if($oid == 3){
                $productsS1 = $currVendor->products()->has('offersSpecial2')->get();
                $productsS2 = $currVendor->products()->has('offersProduct2')->get();
                $productsS3 = $currVendor->products()->whereHas('offersCategory2', function ($query) use ($id) {
                    $query->where('vendor_id','=',$id)->has('main');
                })->get();
                $productsS4 = $currVendor->products()->has('offersVendor2')->get();
                $allProducts = $productsS1->merge($productsS2);
                $allProducts = $allProducts->merge($productsS3);
                $allProducts = $allProducts->merge($productsS4);
                $productsS = $allProducts;
            } else if($oid == 4){
                $productsS = $currVendor->products()->where([['status', 1], ['vstatus', 1]])->orderBy('price', 'ASC')->paginate(10);
            } else if($oid == 5){
                $productsS = $currVendor->products()->where([['status', 1], ['vstatus', 1]])->orderBy('price', 'DESC')->paginate(10);
            }
            $products = ProductResource::collection($productsS);
            return $products;
        }
    }

    public function getCategoryInfo($id)
    {
        if(is_numeric($id)){
            $category = Category::findOrFail($id);
            if (Cache::has('cat'.$id)) {
                $baseCategoryIds = Cache::get('cat'.$id);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$id, function () use ($id) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $id)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(10);
            $products = ProductResource::collection($productsS);
            return [
                'category' => $category,
                'parent' => $category->parents->name,
                'subcategory' => $category->childen,
                'products' => $products,
            ];
        }
    }

    public function getVendorCategoryInfo($id, $vid)
    {
        if(is_numeric($id) && is_numeric($vid)){
            $category = Category::findOrFail($id);
            $vendor = Vendor::findOrFail($vid);
            if (Cache::has('cat'.$id)) {
                $baseCategoryIds = Cache::get('cat'.$id);
            } else {
                $baseCategoryIds = Cache::rememberForever('cat'.$id, function () use ($id) {
                    $data = Category::with(['products', 'childrenRecursive'])->where('id', $id)->get()->toArray();
                    return Arr::pluck($this->flatten($data), 'id');
                });
            }
            $productsS = Product::whereIn('category_id', $baseCategoryIds)->where('vendor_id', $vid)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'ASC')->paginate(10);
            $products = ProductResource::collection($productsS);
            return [
                'category' => $category,
                'vendor' => $vendor->name,
                'products' => $products,
            ];
        }
    }
	
	public function getStories()
	{
        $nowTime = date('Y-m-d H:i:s');
		// $stories = Story::where('cactive', 1)->whereHas('items', function ($query) use ($nowTime) {
        //     // $query->where('cactive', '=', '1');
        //     $query->where('cactive', '=', '1')->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime);
        // })->get();
        $stories = Story::where('cactive', 1)->whereHas('items', function ($query) use ($nowTime) {
            $query->where('cactive', '=', '1')->where('end_story', '>', $nowTime)->where('start_story', '<', $nowTime);
            // $query->where('cactive', '=', '1')->where('start_story', '>', $nowTime);
        })->get();
		return [ 'data' => StoriesResource::collection($stories)];
	}

    public function getStoryLink($id)
    {
        if(is_numeric($id)){
            $story = StoryItem::find($id);
            if($story){
                return $this->StoryLinkParse($story->link);
            }
        }
        return ['status'=>'error', 'message'=>'Link i gabuar'];
    }

    protected function StoryLinkParse($url){
        $re = '/^(?:(?:www\.)?(?:.*?))\.(?:com|al)\/(.*)/m';
        preg_match($re,trim($url),$match);
        if($match && $match[1]){
            preg_match('/^(?:(?:https|http)?(?::\/\/))?(?:www.)?(elefandi.com)/m',trim($url),$matchUrl);
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
        return ['status'=>'error', 'message'=>'Link i gabuar'];
    }
	
	public function scanQrCode(Request $request)
	{
        $url = $request;
        // $url = 'https://elefandi.com/code-it/361';
        $re = '/^(?:(?:www\.)?(?:.*?))\.(?:com|al)\/(.*)/m';
        preg_match($re,trim($url),$match);
        if($match && $match[1]){
            preg_match('/^(?:(?:https|http)?(?::\/\/))?(?:www.)?(elefandi.com)/m',trim($url),$matchUrl);
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
        return ['status'=>'error', 'message'=>'Link i gabuar'];
	}

    public function helpPage()
    {
        $helpPage = Pages::findOrFail(10);
        return ['status'=>'success', 'title'=> $helpPage->name, 'message'=>$helpPage->description];
    }

    public function helpVendor()
    {
        $helpPage = Pages::findOrFail(13);
        return ['status'=>'success', 'title'=> $helpPage->name, 'message'=>$helpPage->description];
    }

    public function getOffer($id)
    {
        if(is_numeric($id)){
            $offer = Offer::findOrFail($id);
            $todayDate = date('Y-m-d H:i:s');
            if($offer->start_date < $todayDate && $offer->expire_date > $todayDate){
                $productsS = $offer->details()->whereHas('product', function($q){
                    $q->whereHas('owner', function($q){
                        $q->where('vstatus', '=', 1);
                    })->where([['status', 1], ['vstatus', 1]]);
                })->paginate(10);
                // return $productsS;
                
                // $productsS = Product::whereIn('category_id', $baseCategoryIds)->where([['status', 1], ['vstatus', 1]])->paginate(10);
                $products = OfferProductResoruce::collection($productsS);
                return [
                    'offer' => $offer,
                    'products' => $products,
                ];
            }
        }
        return [
            'offer' => [
                'name' => 'e pa Vlefshme'
            ],
            'products' => [],
        ];
    }
}
