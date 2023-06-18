<?php

namespace App\Http\Livewire;

use App\Models\City;
use App\Models\Country;
use Livewire\Component;

class SelectCountries extends Component
{
    public $countries;
    public $cities;
    public $selCountry;
    public $selCity = 0;
    public $disabled = false;

    public function mount()
    {
        $this->countries = Country::all();
        if($this->selCountry){
            $this->cities = City::where('country_id', $this->selCountry)->where('status', '=', 1)->get();
        }
    }

    public function render()
    {
        return view('livewire.select-countries');
    }

    public function updatedSelCountry()
    {
        $this->cities = City::where('country_id', $this->selCountry)->where('status', '=', 1)->get();
        $this->selCity = '';
    }
}
