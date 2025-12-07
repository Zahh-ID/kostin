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

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $tenant = $request->user();

        $item = WishlistItem::firstOrCreate([
            'user_id' => $tenant->id,
            'property_id' => $request->property_id,
        ]);

        return response()->json([
            'message' => 'Added to wishlist',
            'data' => $item,
        ]);
    }

    public function destroy(Request $request, $propertyId): JsonResponse
    {
        $tenant = $request->user();

        WishlistItem::where('user_id', $tenant->id)
            ->where('property_id', $propertyId)
            ->delete();

        return response()->json([
            'message' => 'Removed from wishlist',
        ]);
    }
}
