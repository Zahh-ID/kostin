<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): JsonResponse
    {
        Log::info('Google Auth Redirect Requested');
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            Log::info('Google Auth Redirect URL generated: ' . $url);
            return response()->json([
                'url' => $url,
            ]);
        } catch (\Exception $e) {
            Log::error('Google Auth Redirect Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to generate redirect URL'], 500);
        }
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback(): JsonResponse
    {
        Log::info('Google Auth Callback Hit');
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            Log::info('Google User retrieved: ' . $googleUser->getEmail());
        } catch (\Exception $e) {
            Log::error('Google Auth Callback Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Google authentication failed.',
                'error' => $e->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Update Google ID if not set
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(str()->random(16)), // Random password
                'role' => 'tenant', // Default role
                'email_verified_at' => now(),
            ]);
        }

        if ($user->suspended_at) {
            return response()->json([
                'message' => 'Your account has been suspended.',
            ], Response::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'is_new_user' => $user->wasRecentlyCreated,
        ]);
    }
}
