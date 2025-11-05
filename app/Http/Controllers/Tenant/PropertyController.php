<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function create()
    {
        return view('owner.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $property = Property::create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'address' => $validated['address'],
        ]);

        $request->user()->update(['role' => 'owner']);

        return redirect()->route('owner.properties.show', $property);
    }
}
