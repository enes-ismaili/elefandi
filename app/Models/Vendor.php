<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Vendor extends Model
{
    use Sluggable;

    public function owners()
    {
        return $this->belongsToMany(User::class, 'vendor_roles');
    }

    public function ownersRequest()
    {
        return $this->hasMany(UserRoleRequest::class, 'vendor_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id')->first();
    }

    public function cities()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function shippings()
    {
        return $this->hasMany(Transport::class, 'vendor_id', 'id');
    }

    public function socials()
    {
        return $this->hasMany(SocialLink::class, 'vendor_id', 'id');
    }

    public function workhour()
    {
        return $this->hasOne(WorkHour::class, 'vendor_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'vendor_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'vendor_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(OrderVendor::class, 'vendor_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'vendor_id')->orderBy('id', 'DESC');
    }

    public function namerequest()
    {
        return $this->hasOne(VendorNameRequest::class, 'vendor_id', 'id');
    }

    public function pages()
    {
        return $this->hasOne(VendorPages::class, 'vendor_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'vendor_id');
    }

    public function membership()
    {
        return $this->hasMany(VendorMembership::class, 'vendor_id')->where('active', 1);
    }

    public function amembership()
    {
        $now = date('Y-m-d H:i');
        return $this->hasMany(VendorMembership::class, 'vendor_id')->where('active', 1)->where('end_date', '>', $now);
    }

    public function invoices()
    {
        return $this->hasMany(MembershipInvoice::class, 'vendor_id');
    }

    public function unpaidInvoices()
    {
        return $this->hasMany(MembershipInvoice::class, 'vendor_id')->where('paid', 0);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'vendor_id', 'id');
    }

    public function ads()
    {
        return $this->hasMany(AdsSingle::class, 'vendor_id', 'id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'vendor_id', 'id');
    }

    public function storie()
    {
        return $this->hasOne(Story::class, 'vendor_id', 'id');
    }

    public function generateUvid()
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)).'-'.time();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uvid= $model->generateUvid();
        });
    }
}
