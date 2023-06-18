<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeTrendingCategory extends Model
{
    public $timestamps = false;
    protected $fillable = ['category_id', 'tag_id', 'corder'];
}
