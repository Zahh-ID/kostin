<?php

namespace App\Http\Controllers\Web\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedSearchController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $tenant */
        $tenant = $request->user();

        /** @var LengthAwarePaginator $savedSearches */
        $savedSearches = $tenant->savedSearches()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tenant.saved-searches.index', [
            'savedSearches' => $savedSearches,
        ]);
    }

    public function apply(Request $request, SavedSearch $savedSearch): RedirectResponse
    {
        /** @var User $tenant */
        $tenant = $request->user();

        abort_if($savedSearch->user_id !== $tenant->id, 403);

        $filters = collect($savedSearch->filters ?? [])
            ->mapWithKeys(function ($value, string $key): array {
                if (is_array($value)) {
                    $collection = collect($value)->filter(static fn ($item) => $item !== null && $item !== '');

                    if ($collection->isEmpty()) {
                        return [];
                    }

                    return [$key => $collection->implode(',')];
                }

                if ($value === null || $value === '') {
                    return [];
                }

                return [$key => $value];
            });

        $filters = $filters->put('saved_search', (string) $savedSearch->id);

        return redirect()
            ->route('home', $filters->all())
            ->with('status', __('Filter pencarian diterapkan dari pencarian tersimpan.'));
    }

    public function destroy(Request $request, SavedSearch $savedSearch): RedirectResponse
    {
        /** @var User $tenant */
        $tenant = $request->user();

        abort_if($savedSearch->user_id !== $tenant->id, 403);

        $savedSearch->delete();

        return redirect()
            ->route('tenant.saved-searches.index')
            ->with('status', __('Pencarian tersimpan dihapus.'));
    }
}
