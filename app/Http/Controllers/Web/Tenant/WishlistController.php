<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        /** @var LengthAwarePaginator $wishlistItems */
        $wishlistItems = $tenant->wishlistItems()
            ->with([
                'property.owner:id,name',
                'property.roomTypes.rooms:id,room_type_id,status',
            ])
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('tenant.wishlist.index', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    public function destroy(Request $request, WishlistItem $wishlistItem): RedirectResponse
    {
        /** @var User $tenant */
        $tenant = $request->user();

        abort_if($wishlistItem->user_id !== $tenant->id, 403);

        $wishlistItem->delete();

        return redirect()
            ->route('tenant.wishlist.index')
            ->with('status', __('Properti dihapus dari wishlist.'));
    }
}
