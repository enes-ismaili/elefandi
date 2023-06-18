<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function vendor()
    {
        return $this->belongsToMany(Vendor::class,'vendor_roles')->first();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_roles');
    }

    public function vroles()
    {
        return $this->belongsToMany(Role::class,'user_roles')->where('type', '=', 0);
    }

    public function aroles()
    {
        return $this->belongsToMany(Role::class,'user_roles')->where('type', '=', 1);
    }

    public function country()
    {
        return $this->belongsTo(Country::class)->first();
    }

    public function cities()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function cart()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    public function wishlist()
    {
        return $this->hasMany(WishList::class);
    }

    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class,'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id')->orderBy('id', 'DESC');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id')->orderBy('id', 'DESC');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function vendorRequest()
    {
        return $this->hasOne(VendorRequest::class, 'user_id', 'id');
    }

    public function token()
    {
        return $this->hasOne(UserToken::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class, 'user_id', 'id');
    }

    public function onesignal()
    {
        return $this->hasOne(UserOnesignal::class, 'user_id');
    }

    public function generateUuid()
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)).'-'.time();
    }

    public function generateToken()
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return time().vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }

    public function createToken(string $name)
    {
        $generatedToken = $this->generateToken();
        $token = $this->token()->updateOrCreate(
            ['name' => $name],
            ['token' => $generatedToken]
        );
        return $generatedToken;
    }

    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->email)->send(new ForgetPassword($token, $this));
        // Mail::to($this->email)->send(new PasswordReset($token));
        return $this->email;
        // $this->notify(new PasswordReset($token));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid= $model->generateUuid();
        });
    }
}
