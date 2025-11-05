<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\SharedTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SharedTaskController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()->id;

        /** @var LengthAwarePaginator $tasks */
        $tasks = SharedTask::query()
            ->whereHas('property', fn ($query) => $query->where('owner_id', $ownerId))
            ->with(['property', 'assignee'])
            ->orderByDesc('next_run_at')
            ->paginate(15)
            ->withQueryString();

        return view('owner.shared-tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    public function show(Request $request, SharedTask $sharedTask): View
    {
        $this->ensureOwnerOwnsSharedTask($request->user()->id, $sharedTask);

        $sharedTask->load([
            'property',
            'assignee',
            'logs' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('owner.shared-tasks.show', [
            'sharedTask' => $sharedTask,
        ]);
    }

    public function create(Request $request): View
    {
        $properties = Property::query()
            ->where('owner_id', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('owner.shared-tasks.create', [
            'properties' => $properties,
        ]);
    }

    private function ensureOwnerOwnsSharedTask(int $ownerId, SharedTask $sharedTask): void
    {
        abort_if(optional($sharedTask->property)->owner_id !== $ownerId, 403, 'Tugas tidak ditemukan.');
    }
}
