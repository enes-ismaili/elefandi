<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class SelectUser extends Component
{
    public $pid;
    public $users;
    public $selectedUser = [];
    public $selectedUserId = [];
    public $userS;
    public $edit = true;

    protected $queryString = [
        'userS' => ['except' => '']
    ];

    public function mount()
    {
        if($this->pid){
            $this->selectedUser = User::where('id', '=', $this->pid)->first();
            $this->selectedUserId = $this->pid;
        }
    }

    public function render()
    {
        if(strlen($this->userS) >= 3){
            $queryStrings = '%'.$this->userS.'%';
            $this->users = User::where('first_name', 'like', $queryStrings)->orWhere('last_name', 'like', $queryStrings)->orWhere('email', 'like', $queryStrings)->take(7)->get();
        } else {
            $this->users = [];
        }
        return view('livewire.select-user');
    }

    public function addUser($userId)
    {
        if(is_numeric($userId)){
            $this->selectedUserId = $userId;
            $this->selectedUser = User::where('id', '=', $userId)->first();
            $this->userS = '';
            $this->users = '';
        }
    }

    public function removeUser()
    {
        $this->selectedUser = '';
        $this->selectedUserId = '';
        $this->userS = '';
        $this->users = '';
    }
}
