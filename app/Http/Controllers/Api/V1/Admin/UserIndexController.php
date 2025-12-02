<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserIndexController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $users = User::query()
            ->latest()
            ->limit(50)
            ->get();

        return response()->json([
            'data' => AdminUserResource::collection($users),
        ]);
    }
}
