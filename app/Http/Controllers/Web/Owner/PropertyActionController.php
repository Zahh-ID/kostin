<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyActionController extends Controller
{
    /**
     * Store a new property for the authenticated owner.
     */
    public function store(Request $request)
    {
        $owner = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'rules_text' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'pending', 'approved', 'rejected'])],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['nullable', 'image', 'max:4096'],
            'existing_photos' => ['nullable', 'array'],
            'existing_photos.*' => ['nullable', 'url'],
        ]);

        $photos = $this->mergePhotos($request);

        /** @var Property $property */
        $property = Property::create([
            'owner_id' => $owner->id,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
            'rules_text' => $validated['rules_text'] ?? null,
            'status' => $validated['status'] ?? 'pending',
            'photos' => $photos,
        ]);

        return $this->respond($request, $property->fresh());
    }

    /**
     * @return array<int, string>
     */
    private function mergePhotos(Request $request): array
    {
        $storedPhotos = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $uploaded) {
                if ($uploaded === null) {
                    continue;
                }

                $path = $uploaded->store('properties', 'public');
                $storedPhotos[] = Storage::url($path);
            }
        }

        $existingPhotos = collect($request->input('existing_photos', []))
            ->filter()
            ->values()
            ->all();

        return array_values(array_unique(array_merge($existingPhotos, $storedPhotos)));
    }

    private function respond(Request $request, Property $property)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Properti berhasil dibuat.',
                'data' => $property,
            ], 201);
        }

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('status', 'Properti berhasil dibuat.');
    }
}
