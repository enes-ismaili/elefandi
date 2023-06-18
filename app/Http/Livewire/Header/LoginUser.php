<?php

namespace App\Http\Livewire\Header;

use Hash;
use Livewire\Component;
use App\Models\User;

class LoginUser extends Component
{
    public $users, $email, $password;
    public $showForm = false;
    private $route;
    protected $listeners = [
        'open-login' => 'openModal',
    ];

    public function render()
    {
        return view('livewire.header.login-user');
    }

    public function login()
    {
        $validatedDate = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email është i detyrueshëm',
            'email.email' => 'Email nuk është i saktë',
            'email.required' => 'Fjalkalimi është i detyrueshëm',
        ]);
        if(\Auth::attempt(array('email' => $this->email, 'password' => $this->password, 'status' => 1))){
            $this->route = url()->previous();
            $this->showForm = false;
            session()->flash('message', "Ju keni hyrë me sukses.");
            session()->flash('synclocal', 'true.');
            return redirect($this->route);
        }else{
            session()->flash('error', 'Email ose Fjalkalimi janë gabim');
        }
    }

    public function openModal()
    {
        $this->showForm = true;
    }

    public function openModalOutside()
    {
        $this->showForm = true;
    }

    public function changeCountry($url)
    {
        if($url){
            return redirect()->route('view.cart');
        }
    }

    public function closeModal()
    {
        $this->showForm = false;
    }
}
