<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id', 'id');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'id', 'ticket_id');
    }

    public function attachment()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id', 'id')->where('message_id', 0);
    }
}
