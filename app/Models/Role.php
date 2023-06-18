<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function user()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
