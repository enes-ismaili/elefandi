<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $currentPassword, $newPassword, $confirmNewPassword;
    public $showForm = false;

    public function render()
    {
        return view('livewire.user.change-password');
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

    public function changePass()
    {
        $validatedDate = $this->validate([
            'currentPassword' => ['required', new MatchOldPassword],
            'newPassword' => 'required',
            'confirmNewPassword' => 'required|same:newPassword',
        ], [
            'currentPassword.required' => 'Fjalkalimi Aktual është i detyrueshëm',
            'newPassword.required' => 'Fjalkalimi Aktual është i detyrueshëm',
            'confirmNewPassword.required' => 'Fjalkalimi Aktual është i detyrueshëm',
            'confirmNewPassword.same' => 'Konfirmimi i Fjalkalimit nuk është i njëjtë me Fjalkalimin e ri',
        ]);
        User::find(auth()->user()->id)->update(['password'=> Hash::make($this->newPassword)]);
        return redirect()->route('login');
    }
}
