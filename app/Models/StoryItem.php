<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryItem extends Model
{
    protected $fillable = ['corder'];

    public function main()
    {
        return $this->hasOne(Story::class, 'id', 'stories_id');
    }
}
