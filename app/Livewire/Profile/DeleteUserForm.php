<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public string $password = '';

    public bool $confirmingUserDeletion = false;

    public function confirmUserDeletion(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->password = '';
        $this->confirmingUserDeletion = true;
    }

    public function cancelUserDeletion(): void
    {
        $this->confirmingUserDeletion = false;
        $this->password = '';
    }

    public function deleteUser(): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();

        Auth::logout();

        $user?->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('profile.delete-user-form');
    }
}
