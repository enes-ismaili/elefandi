<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeCategorySlider extends Model
{
    use HasFactory;

    public function getFullimageAttribute()
{
    return asset('photos/category'.$this->image);
}
}
