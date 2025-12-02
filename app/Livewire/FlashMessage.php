<?php

namespace App\Livewire;

use Livewire\Component;

class FlashMessage extends Component
{
    
    public $type;
    public $message;
    public $visible = false;

    protected $listeners = ['flashMessage' => 'showMessage'];

    public function showMessage($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        $this->visible = true;

        // Hide automatically after 3 seconds
        $this->dispatch('autoHideFlash');
    }

    #[\Livewire\Attributes\On('hideFlash')]
    public function hideFlash()
    {
        $this->visible = false;
    }

    public function render()
    {
        return view('livewire.flash-message');
    }
}
