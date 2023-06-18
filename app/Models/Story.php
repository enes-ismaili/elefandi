<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    public function items()
    {
        return $this->hasMany(StoryItem::class, 'stories_id', 'id')->orderBy('corder');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }
}
