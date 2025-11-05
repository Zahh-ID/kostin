<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $owner = $request->user();

        /** @var LengthAwarePaginator $properties */
        $properties = $owner->properties()
            ->with([
                'roomTypes.rooms',
                'sharedTasks' => fn ($query) => $query->latest()->limit(2),
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.properties.index', [
            'properties' => $properties,
        ]);
    }

    public function show(Request $request, Property $property): View
    {
        $property->load([
            'roomTypes.rooms.contracts' => fn ($query) => $query->latest('start_date')->limit(1),
            'sharedTasks.logs' => fn ($query) => $query->latest()->limit(3),
        ]);

        return view('owner.properties.show', [
            'property' => $property,
        ]);
    }

    public function create(Request $request): View
    {
        return view('owner.properties.create', [
            'statuses' => $this->allowedStatuses(),
        ]);
    }

    public function edit(Request $request, Property $property): View
    {
        $property->load(['roomTypes.rooms']);

        return view('owner.properties.edit', [
            'property' => $property,
            'statuses' => $this->allowedStatuses(),
        ]);
    }

    /**
     * @return Collection<int, string>
     */
    private function allowedStatuses(): Collection
    {
        return collect(['draft', 'pending', 'approved', 'rejected']);
    }
}
