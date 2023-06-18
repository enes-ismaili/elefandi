<?php

namespace App\Http\Livewire;

use Livewire\Component;

class IconPicker extends Component
{
    public $selectedIcon = 'fas fa-horse';
    
    public function render()
    {
        return view('livewire.icon-picker');
    }
}
