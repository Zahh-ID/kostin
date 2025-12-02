<?php

namespace App\Http\Controllers\Api\V1\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\OwnerPropertyPhotoRequest;
use App\Models\Property;
use App\Http\Resources\Owner\OwnerPropertyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyPhotoController extends Controller
{
    public function __invoke(OwnerPropertyPhotoRequest $request, Property $property): JsonResponse
    {
        $file = $request->file('photo');
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('property-photos', $filename, 'public');

        $url = Storage::disk('public')->url($path);
        $photos = $property->photos ?? [];
        $photos[] = $url;
        $property->update([
            'photos' => array_values(array_unique($photos)),
        ]);

        $this->recordAudit('property.photo.upload', 'property', $property->id, ['path' => $path]);

        return response()->json([
            'url' => $url,
            'path' => $path,
            'property' => new OwnerPropertyResource($property->fresh()->loadCount('roomTypes')),
        ]);
    }
}
