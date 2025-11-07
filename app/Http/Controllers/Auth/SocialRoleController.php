<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SocialRoleController extends Controller
{
    public function create(Request $request): RedirectResponse|View
    {
        if (! session()->has('socialite_google_user')) {
            return redirect()->route('login')->with('status', __('Sesi Google sudah kedaluwarsa. Silakan ulangi proses login.'));
        }

        return view('auth.social-role');
    }

    public function store(SocialRoleRequest $request): RedirectResponse
    {
        $socialUser = session('socialite_google_user');

        if (! $socialUser) {
            return redirect()->route('login')->with('status', __('Sesi Google sudah kedaluwarsa. Silakan ulangi proses login.'));
        }

        $user = User::create([
            'name' => $socialUser['name'] ?? 'Google User',
            'email' => $socialUser['email'],
            'google_id' => $socialUser['id'],
            'role' => $request->input('role'),
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        session()->forget('socialite_google_user');

        return redirect()->route('dashboard');
    }
}
