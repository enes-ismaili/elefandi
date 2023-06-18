<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $fillable = ['name', 'token'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
