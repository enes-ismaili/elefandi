<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoleRequest extends Model
{
    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
