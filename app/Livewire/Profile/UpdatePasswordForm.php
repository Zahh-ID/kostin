<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class UpdatePasswordForm extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()?->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('status', 'password-updated');
    }

    public function render()
    {
        return view('profile.update-password-form');
    }
}
