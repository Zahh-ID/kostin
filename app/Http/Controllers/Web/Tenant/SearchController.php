<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = Property::query()
            ->where('status', 'approved')
            ->with([
                'roomTypes.rooms' => function ($roomQuery) {
                    $roomQuery->where('status', 'available');
                },
            ])
            ->withCount(['roomTypes as available_rooms_count' => function ($roomTypeQuery) {
                $roomTypeQuery->whereHas('rooms', fn ($rooms) => $rooms->where('status', 'available'));
            }]);

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($roomTypeName = $request->string('room_type')->toString()) {
            $query->whereHas('roomTypes', fn ($roomTypes) => $roomTypes->where('name', 'like', "%{$roomTypeName}%"));
        }

        $priceMin = $request->integer('price_min');
        $priceMax = $request->integer('price_max');

        if ($priceMin) {
            $query->whereHas('roomTypes', fn ($roomTypes) => $roomTypes->where('base_price', '>=', $priceMin));
        }

        if ($priceMax) {
            $query->whereHas('roomTypes', fn ($roomTypes) => $roomTypes->where('base_price', '<=', $priceMax));
        }

        if ($request->boolean('available_only')) {
            $query->whereHas('roomTypes.rooms', fn ($rooms) => $rooms->where('status', 'available'));
        }

        /** @var LengthAwarePaginator $properties */
        $properties = $query->latest()->paginate(9)->withQueryString();

        $properties->getCollection()->transform(function (Property $property) {
            $property->min_price = $property->roomTypes->pluck('base_price')->filter()->min();
            $property->preview_rules = Str::limit($property->rules_text, 160);
            $property->cover_url = $property->photos[0] ?? 'https://picsum.photos/seed/'.($property->id ?? Str::random(6)).'/600/400';

            return $property;
        });

        return view('tenant.search', [
            'properties' => $properties,
        ]);
    }
}
