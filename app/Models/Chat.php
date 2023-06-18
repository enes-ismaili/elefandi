<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->where('type', 1)->orderBy('id', 'DESC')->latest();
    }

    public function latestMessageU()
    {
        return $this->hasOne(ChatMessage::class)->where('way', 2)->latest();
    }

    public function latestMessageV()
    {
        return $this->hasOne(ChatMessage::class)->where('way', 1)->orderBy('id', 'DESC')->latest();
    }

    public function userOnesignal()
    {
        return $this->hasOne(UserOnesignal::class, 'user_id', 'user_id');
    }

    public function vendorLiveStatus()
    {
        $currVendor = $this->vendor;
        $today = Str::lower(date('l'));
        $nowTime = date('H:i:00');
        $date1 = Carbon::createFromFormat('H:i:s', $nowTime);
        $date2 = Carbon::createFromFormat('H:i:s', $currVendor->workhour[$today.'_start']);
        $resultS = $date1->gt($date2);
        $date3 = Carbon::createFromFormat('H:i:s', $nowTime);
        $date4 = Carbon::createFromFormat('H:i:s', $currVendor->workhour[$today.'_end']);
        $resultE = $date3->lt($date4);
        if($currVendor->workhour[$today] && $resultS && $resultE){
            return true;
        }
        return false;
    }
}
