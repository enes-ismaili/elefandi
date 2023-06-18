<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeCategoryProduct extends Model
{
    public $timestamps = false;
    protected $fillable = ['category_id', 'children_id', 'corder'];
}
