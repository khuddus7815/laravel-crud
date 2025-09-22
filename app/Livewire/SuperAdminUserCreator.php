<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminUserCreator extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $is_admin = 0; // Default to user
    public $initialIsAdmin;

    public function mount($initialIsAdmin = 0)
    {
        $this->initialIsAdmin = $initialIsAdmin;
        $this->is_admin = $initialIsAdmin;
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['required', 'boolean'],
        ];
    }

    public function saveUser()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_admin' => $this->is_admin,
        ]);

        session()->flash('message', 'User/Admin created successfully.');
        $this->reset(); // Clear form fields
        $this->dispatch('userCreated'); // Emit event to refresh parent component
    }

    public function render()
    {
        return view('livewire.super-admin-user-creator');
    }
}
