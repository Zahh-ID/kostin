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

        $existing = User::where('email', $googleUser->email)->first();

        if ($existing) {
            $existing->update([
                'name' => $googleUser->name ?? $existing->name,
                'google_id' => $googleUser->id,
            ]);

            Auth::login($existing);

            return redirect()->route('dashboard');
        }

        session([
            'socialite_google_user' => [
                'id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'token' => $googleUser->token,
            ],
        ]);

        return redirect()->route('auth.social-role');
    }
}
