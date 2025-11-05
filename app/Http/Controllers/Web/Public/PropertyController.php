<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display property details for public visitors.
     */
    public function __invoke(Request $request, Property $property): View
    {
        abort_if($property->status !== 'approved', 404);

        $property->load([
            'owner:id,name,phone',
            'roomTypes.rooms' => fn ($query) => $query->select('id', 'room_type_id', 'room_code', 'status', 'custom_price'),
            'sharedTasks' => fn ($query) => $query->latest()->limit(5),
        ]);

        return view('public.property', [
            'property' => $property,
            'activeTab' => $request->query('tab', 'overview'),
        ])->layout('layouts.public');
    }
}
