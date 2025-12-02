<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user();

        $items = WishlistItem::query()
            ->where('user_id', $tenant->id)
            ->get(['id', 'property_id']);

        return response()->json([
            'data' => $items,
        ]);
    }
}
