<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Product extends Model
{
    use Sluggable;

    public function productStatus()
    {
        $productStatus = $this->status;
        if($productStatus){
            return 'Aktiv';
        }
        return 'Jo Aktiv';
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'product_brands', 'product_id', 'brand_id')->withTimestamps();
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function allCategories()
    {
        $allCategories = [];
        $currCategory = $this->category;
        if($currCategory){
            $allCategories[] = $currCategory;
            if($currCategory->parents){
                $pCategory = $currCategory->parents;
                $allCategories[] = $pCategory;
                if($pCategory->parents){
                    $allCategories[] = $pCategory->parents;
                }
            }
            return collect($allCategories);
        }
        return collect([]);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id')->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function shippings()
    {
        return $this->hasMany(ProductShipping::class);
    }

    public function owner()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function cart()
    {
        return $this->hasMany(ShoppingCart::class, 'product_id', 'id');
    }

    public function wishlist()
    {
        return $this->hasMany(WishList::class, 'product_id', 'id');
    }

    public function cartVariant($ids)
    {
        if($ids){
            return ProductVariant::where('id', '=', $ids)->first();
        }
        return NULL;
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    public function specification()
    {
        return $this->hasMany(ProductSpecification::class, 'product_id', 'id');
    }

    public function psales()
    {
        return $this->hasOne(ProductSales::class, 'product_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class, 'product_id', 'id');
    }

    public function offersSpecial()
    {
        return $this->hasMany(OfferDetail::class, 'prod_id', 'id')->where('type', 4);
    }

    public function offersProduct()
    {
        return $this->hasMany(OfferDetail::class, 'prod_id', 'id')->where('type', 3);
    }

    public function reports()
    {
        return $this->hasMany(ProductReports::class, 'product_id', 'id');
    }

    public function offersCategory()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        // $categoriesArray = [];
        // $category = $this->category;
        // if($category){
        //     $categoriesArray[] = $category->id;
        //     if($category->parent){
        //         $secondLevel = Category::find($category->parent);
        //         $categoriesArray[] = $secondLevel->id;
        //         if($secondLevel->parent) {
        //             $thirdLevel = Category::find($secondLevel->parent);
        //             $categoriesArray[] = $thirdLevel->id;
        //         }
        //     }
        // }
        return OfferDetail::where('vendor_id', '=', $this->vendor_id)->where('prod_id', $this->category_id)->where('type', '=', 2)
            ->whereHas('main', function($q) use ($now) {
                $q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);
            })->first();
    }

    public function offersVendor()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return OfferDetail::where([['type', 1],['prod_id', '=', $this->owner->id]])->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->first();
    }

    public function offers($productVariant=0)
    {
        if(!$productVariant) $productVariant=0;
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $thisOffer = $this->offersSpecial()->where('variant_id', $productVariant)->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})
            ->where('discount', '!=', '0')->orderBy('discount', 'asc')->first();
        if(!$thisOffer){
            $thisOffer = $this->offersProduct()->where('variant_id', $productVariant)->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})
            ->where('discount', '!=', '0')->orderBy('discount', 'asc')->first();
        }
        if(!$thisOffer){
            $thisOffer = $this->offersCategory();
        }
        if(!$thisOffer){
            $thisOffer = $this->offersVendor();
        }
        return $thisOffer;
    }

    public function minoffers()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $thisOffer = $this->offersSpecial()->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->where('discount', '!=', '0')->orderBy('discount', 'asc')->first();
        if(!$thisOffer){
            $thisOffer = $this->offersProduct()->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->where('discount', '!=', '0')->orderBy('discount', 'asc')->first();
        }
        if(!$thisOffer){
            $thisOffer = $this->offersCategory();
        }
        if(!$thisOffer){
            $thisOffer = $this->offersVendor();
        }
        return $thisOffer;
    }

    public function offersSpecial2()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $this->hasOne(OfferDetail::class, 'prod_id', 'id')->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->where('type', 4)->where('discount', '!=', '0')->orderBy('discount', 'asc');
    }

    public function offersVendor2()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $this->hasOne(OfferDetail::class, 'prod_id', 'vendor_id')->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->where('type', 1)->where('discount', '!=', '0')->orderBy('discount', 'asc');
    }

    public function offersProduct2()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $this->hasMany(OfferDetail::class, 'prod_id', 'id')->where('type', 3)->whereHas('main', function($q) use ($now) {$q->where([['active', '=', 1],['start_date', '<=', $now],['expire_date', '>=', $now]]);})->where('discount', '!=', '0')->orderBy('discount', 'asc');
    }

    public function offersCategory2()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return $this->hasMany(OfferDetail::class, 'prod_id', 'category_id')->where('type', '=', 2);
    }

    public function minoffers2()
    {
        $thisOffer = $this->offersSpecial2();
        if($thisOffer != null){
            //$thisOffer = $this->offersProduct2();
        }
        if($thisOffer != null){
            $thisOffer = $this->offersCategory2();
        }
        if($thisOffer != null){
            // return $thisOffer = $this->offersProduct2();
            // $thisOffer = $this->offersVendor2();
        }
        return $thisOffer;
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function similarProducts()
    {
        $prodId = $this->id;
        if($this->category){
            $categoryArray = array($this->category->id);
            if($this->category->parent != 0){
                $newCategory = $this->category->parents;
                array_push($categoryArray, $this->category->parents->id);
                if($newCategory->parent != 0) {
                    array_push($categoryArray, $newCategory->parents->id);
                }
            }
            $similarProducts = Product::whereIn('category_id', $categoryArray)->whereHas('owner', function($q){$q->where('vstatus', '=', 1);})
                ->where('status', '=', 1)->where('id', '!=', $prodId)->where([['status', 1], ['vstatus', 1]])->orderBy('updated_at', 'DESC')->limit(10)->get();
            return $similarProducts;
        } else {
            return Product::inRandomOrder()->where([['status', 1], ['vstatus', 1]])->limit(5)->get();
        }
    }
}
