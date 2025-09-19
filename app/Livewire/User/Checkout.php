<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Checkout extends Component
{
    public function render()
    {
        return view('livewire.user.checkout');
    }
}
