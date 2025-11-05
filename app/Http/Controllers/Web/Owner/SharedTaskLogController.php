<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\SharedTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SharedTaskLogController extends Controller
{
    public function index(Request $request, SharedTask $sharedTask): View
    {
        $this->ensureOwnerOwnsSharedTask($request->user()->id, $sharedTask);

        /** @var LengthAwarePaginator $logs */
        $logs = $sharedTask->logs()
            ->with(['completedBy:id,name'])
            ->latest('run_at')
            ->paginate(15)
            ->withQueryString();

        return view('owner.shared-task-logs.index', [
            'sharedTask' => $sharedTask,
            'logs' => $logs,
        ]);
    }

    private function ensureOwnerOwnsSharedTask(int $ownerId, SharedTask $sharedTask): void
    {
        abort_if(optional($sharedTask->property)->owner_id !== $ownerId, 403, 'Log tugas tidak ditemukan.');
    }
}
