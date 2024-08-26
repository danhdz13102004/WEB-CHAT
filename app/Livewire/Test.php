<?php

namespace App\Livewire;

use Livewire\Component;

class Test extends Component
{
    public $name = 'hehe';
    
    public function changeName($newName)
    {
        $this->name = $newName;
    }
    public function render()
    {
        return view('livewire.test');
    }

}
