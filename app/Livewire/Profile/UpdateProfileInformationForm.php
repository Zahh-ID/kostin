<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateProfileInformationForm extends Component
{
    public User $user;

    public string $name = '';

    public string $email = '';

    public bool $saved = false;

    public bool $verificationLinkSent = false;

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function updateProfileInformation(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user->id],
        ]);

        $this->user->forceFill([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();

        $this->saved = true;
    }

    public function sendEmailVerification(): void
    {
        if ($this->user?->hasVerifiedEmail()) {
            return;
        }

        $this->user?->sendEmailVerificationNotification();
        $this->verificationLinkSent = true;
    }

    public function render()
    {
        return view('profile.update-profile-information-form');
    }
}
