<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var LengthAwarePaginator $properties */
        $properties = Property::query()
            ->where('status', 'pending')
            ->with('owner:id,name,email')
            ->orderBy('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.moderations.index', [
            'properties' => $properties,
        ]);
    }

    public function approve(Property $property): RedirectResponse
    {
        $property->update(['status' => 'approved']);

        return redirect()
            ->route('admin.moderations.index')
            ->with('status', "Properti {$property->name} telah disetujui.");
    }

    public function reject(Property $property): RedirectResponse
    {
        $property->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.moderations.index')
            ->with('status', "Properti {$property->name} ditandai sebagai ditolak.");
    }
}
