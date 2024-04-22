<?php

namespace App\Livewire;

use Livewire\Component;

class Header extends Component
{
    public $show = false;
 
    public function toggle()
    {
        $this->show = !$this->show;
    }

    public function render()
    {
        return view('livewire.header');
    }
}
