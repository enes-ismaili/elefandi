<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    use Sluggable;

    protected $fillable = ['corder'];

    public function parents()
    {
       return $this->hasOne('App\Models\Category', 'id', 'parent');
    }
    
    public function children()
    {
       return $this->hasMany('App\Models\Category', 'parent', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
        // return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id');
    }

    public function allProducts()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
        // return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id');
    }
    
    public function productsCust($id)
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
        // return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id');
    }

    public function childrenRecursive() {
        return $this->children()->with('childrenRecursive');
    }

    public function trendingtag()
    {
        return $this->belongsToMany(Tag::class, 'home_trending_categories', 'category_id', 'tag_id')->orderBy('corder', 'asc');
    }

    public function categoryChildHome()
    {
        return $this->belongsToMany(Category::class, 'home_category_products', 'category_id', 'children_id')->orderBy('corder', 'asc');
    }

    public function sliders()
    {
        return $this->hasMany(HomeCategorySlider::class, 'category_id', 'id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
