<?php

namespace App\Http\Livewire\User;

use App\Models\City;
use App\Models\Country;
use Livewire\Component;
use App\Models\UserAddress;

class ChangeAddress extends Component
{
    public $addressType = false;
    public $showForm = false;
    public $addressName, $addressPerson, $addressPhone, $addressAddress, $addressAddress2, $addressPostalCode, $addressCity, $addressCityName, $addressState, $addressStateName;
    public $countries;
    public $currentAddress;
    public $changeState = true;
    public $cchangeState = true;
    public $addedAddress = [];
    public $cities = [];
    public $needReload = true;
    public $isPrimary = false;

    public $deleteForm = false;

    public function mount($type, $id=false)
    {
        if($type == 'add'){
            $this->addressType = true;
        } elseif($type == 'edit'){
            $thisAddress = UserAddress::find($id);
            $this->currentAddress = $thisAddress;
            $this->addressType = false;
            $this->addressPerson = $thisAddress->name;
            $this->addressPhone = $thisAddress->phone;
            $this->addressAddress = $thisAddress->address;
            $this->addressAddress2 = $thisAddress->address2;
            $this->addressPostalCode = $thisAddress->zipcode;
            $this->addressCity = $thisAddress->city;
            $this->addressCityName = (is_numeric($thisAddress->city) && $thisAddress->country_id < 4)?$thisAddress->cityF->name:$thisAddress->city;
            $this->addressState = $thisAddress->country_id;
            $this->addressStateName = $thisAddress->country->name;
            if($thisAddress->primary){
                $this->isPrimary = true;
            }
        }
        $this->countries = Country::where('shipping', '=', 1)->get();
        if($this->addressState){
            $this->cities = City::where('country_id', $this->addressState)->get();
        }
    }

    public function render()
    {
        return view('livewire.user.change-address');
    }

    public function openModal($show)
    {
        if($show == 'true'){
            $this->showForm = true;
        } else {
            $this->showForm = false;
        }
    }

    public function closeModal()
    {
        $this->showForm = false;
    }

    public function addAddress()
    {
        $validatedDate = $this->validate([
            'addressPerson' => 'required',
            'addressPhone' => 'required',
            'addressAddress' => 'required',
            'addressCity' => 'required|min:1|not_in:0',
            'addressState' => 'required|min:1|not_in:0',
        ], [
            'addressPerson.required' => 'Personi kontaktues është i detyrueshëm',
            'addressPhone.required' => 'Numri i telefonit është i detyrueshëm',
            'addressAddress.required' => 'Adresa është e detyrueshme',
            'addressCity.required' => 'Qyteti është i detyrueshëm',
            'addressCity.min' => 'Qyteti është i detyrueshëm',
            'addressCity.not_in' => 'Qyteti është i detyrueshëm',
            'addressState.required' => 'Shteti është i detyrueshëm',
            'addressState.min' => 'Shteti është i detyrueshëm',
            'addressState.not_in' => 'Shteti është i detyrueshëm',
        ]);
        $address = new UserAddress();
        $address->user_id = auth()->user()->id;
        $address->name = $this->addressPerson;
        $address->phone = $this->addressPhone;
        $address->address = $this->addressAddress;
        $address->address2 = $this->addressAddress2;
        $address->zipcode = $this->addressPostalCode;
        $address->city = $this->addressCity;
        $address->country_id = $this->addressState;
        if(current_user()->addresses->where('primary', '=', 1)->count() == 0){
            $address->primary = 1;
        }
        $address->save();
        $this->addedAddress[] = $address;
        $this->currentAddress = '';
        $this->addressName = '';
        $this->addressPerson = '';
        $this->addressPhone = '';
        $this->addressAddress = '';
        $this->addressAddress2 = '';
        $this->addressPostalCode = '';
        $this->addressCity = '';
        $this->addressState = '';
        $this->showForm = false;
        if($this->needReload){
            session()->put('success','Adresa u shtua me sukses.');
            return redirect()->route('profile.address');
        } else {
            $this->emitTo('products.buy-directly', 'addressAdded');
        }
        
    }

    public function saveAddress()
    {
        $validatedDate = $this->validate([
            'addressPerson' => 'required',
            'addressPhone' => 'required',
            'addressAddress' => 'required',
            'addressCity' => 'required|min:1|not_in:0',
            'addressState' => 'required|min:1|not_in:0',
        ], [
            'addressPerson.required' => 'Personi kontaktues është i detyrueshëm',
            'addressPhone.required' => 'Numri i telefonit është i detyrueshëm',
            'addressAddress.required' => 'Adresa është e detyrueshme',
            'addressCity.required' => 'Qyteti është i detyrueshëm',
            'addressCity.min' => 'Qyteti është i detyrueshëm',
            'addressCity.not_in' => 'Qyteti është i detyrueshëm',
            'addressState.required' => 'Shteti është i detyrueshëm',
            'addressState.min' => 'Shteti është i detyrueshëm',
            'addressState.not_in' => 'Shteti është i detyrueshëm',
        ]);
        $address = UserAddress::findOrFail($this->currentAddress->id);
        $address->name = $this->addressPerson;
        $address->phone = $this->addressPhone;
        $address->address = $this->addressAddress;
        $address->address2 = $this->addressAddress2;
        $address->zipcode = $this->addressPostalCode;
        $address->city = $this->addressCity;
        $address->country_id = $this->addressState;
        if(current_user()->addresses->where('primary', '=', 1)->count() == 0){
            $address->primary = 1;
        }
        $address->save();
        $this->showForm = false;
        session()->put('success','Adresa u ndryshua me sukses.');
        return redirect()->route('profile.address');
    }

    public function openDeleteModal()
    {
        $this->deleteForm = true;
    }

    public function closeDeleteModal()
    {
        $this->deleteForm = false;
    }

    public function deletePost()
    {
        # code...
        $thisAddress = UserAddress::findOrFail($this->currentAddress->id);
        if($thisAddress){
            $thisAddress->udelete = 1;
            $thisAddress->save();
            session()->put('success','Adresa u fshi me sukses.');
            return redirect()->route('profile.address');
        }
        session()->put('error','Një gabim ka ndodhur. Adresa nuk u fshi.');
    }

    public function updatedAddressState()
    {
        $this->cities = City::where('country_id', $this->addressState)->get();
        $this->addressCity = '';
    }

    public function makePrimary()
    {
        $primaryAddress = current_user()->addresses->where('primary', '=', 1)->first();
        if($primaryAddress){
            $primaryAddress->primary = 0;
            $primaryAddress->save();
        }
        $address = $this->currentAddress;
        $address->primary = 1;
        $address->save();
        session()->put('success','Adresa Kryesore u ndryshua.');
        return redirect()->route('profile.address');
    }
}
