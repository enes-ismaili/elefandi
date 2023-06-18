<?php

namespace App\Rules;

use App\Models\Coupon;
use Illuminate\Contracts\Validation\Rule;

class UniqueCoupon implements Rule
{
    protected $cid;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id = 0)
    {
        $this->cid = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Coupon::where([['ucode', '=', $value],['id', '!=', $this->cid]])->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Përdorni një kod kuponi të ndryshëm nga kodet që keni përdorur më parë.';
    }
}
