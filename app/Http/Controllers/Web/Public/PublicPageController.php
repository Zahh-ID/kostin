<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\View\View;

/**
 * Handles publicly accessible pages such as the landing page and static content.
 */
class PublicPageController extends Controller
{
    public function home(): View
    {
        $properties = Property::query()
            ->with(['owner', 'roomTypes' => fn ($query) => $query->select('id', 'property_id', 'name', 'base_price')])
            ->where('status', 'approved')
            ->latest()
            ->take(6)
            ->get();

        return view('public.home', [
            'properties' => $properties,
        ])->layout('layouts.public');
    }

    public function about(): View
    {
        return view('public.about')->layout('layouts.public');
    }

    public function faq(): View
    {
        return view('public.faq')->layout('layouts.public');
    }

    public function privacy(): View
    {
        return view('public.privacy')->layout('layouts.public');
    }

    public function terms(): View
    {
        return view('public.terms')->layout('layouts.public');
    }

    public function contact(): View
    {
        return view('public.contact')->layout('layouts.public');
    }
}
