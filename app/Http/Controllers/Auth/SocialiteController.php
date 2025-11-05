<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('status', 'Tidak dapat menyelesaikan login Google. Silakan coba lagi.');
        }

        $user = User::firstOrNew(['email' => $googleUser->email]);

        if (! $user->exists) {
            $user->role = User::ROLE_TENANT;
            $user->password = Hash::make(Str::random(32));
            $user->email_verified_at = now();
        }

        $user->name = $googleUser->name ?? $user->name ?? 'Google User';
        $user->google_id = $googleUser->id;

        $user->save();

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
