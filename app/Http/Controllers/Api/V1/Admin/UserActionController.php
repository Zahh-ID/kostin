<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserActionController extends Controller
{
    public function suspend(User $user, Request $request): JsonResponse
    {
        if ($user->role === User::ROLE_ADMIN) {
            return response()->json(['message' => 'Cannot suspend an admin user.'], 403);
        }

        $user->update(['suspended_at' => now()]);

        return response()->json([
            'message' => 'User suspended successfully.',
            'data' => $user,
        ]);
    }

    public function activate(User $user): JsonResponse
    {
        $user->update(['suspended_at' => null]);

        return response()->json([
            'message' => 'User activated successfully.',
            'data' => $user,
        ]);
    }
}
