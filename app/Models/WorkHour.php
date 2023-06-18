<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkHour extends Model
{
    protected $fillable = ['vendor_id', 'monday', 'monday_start', 'monday_end', 'tuesday', 'tuesday_start', 
        'tuesday_end', 'wednesday', 'wednesday_start', 'wednesday_end', 'thursday', 'thursday_start', 'thursday_end', 
        'friday', 'friday_start', 'friday_end', 'saturday', 'saturday_start', 'saturday_end', 'sunday', 'sunday_start', 'sunday_end'];
}
