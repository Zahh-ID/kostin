<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyApplyController extends Controller
{
    public function __invoke(Request $request, Property $property): View
    {
        abort_if($property->status !== 'approved', 404);

        $property->load(['roomTypes.rooms' => fn ($query) => $query->where('status', 'available')]);

        $hasOverdue = $request->user()?->invoices()
            ->whereIn('invoices.status', ['overdue', 'unpaid'])
            ->exists();

        return view('tenant/applications/apply', [
            'property' => $property,
            'hasOverdue' => $hasOverdue,
            'coverUrl' => $property->photos[0] ?? 'https://picsum.photos/seed/'.($property->id ?? 'property').'/800/500',
        ]);
    }
}
