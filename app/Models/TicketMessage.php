<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function attachment()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id', 'id');
    }
}
