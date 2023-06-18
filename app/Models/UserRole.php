<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_id');
    }

    public function roleD()
    {
        // return $this->belongsToMany(Role::class, 'role_id');
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class, 'user_id');
    }
}
