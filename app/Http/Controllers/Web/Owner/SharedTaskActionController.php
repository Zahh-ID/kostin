<?php

namespace App\Http\Controllers\Web\Owner;

use App\Http\Controllers\Controller;
use App\Models\SharedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SharedTaskActionController extends Controller
{
    public function store(Request $request)
    {
        $ownerId = $request->user()->id;

        $validated = $request->validate([
            'property_id' => [
                'required',
                Rule::exists('properties', 'id')->where('owner_id', $ownerId),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rrule' => ['nullable', 'string', 'max:255'],
            'next_run_at' => ['nullable', 'date'],
            'assignee_user_id' => ['nullable', Rule::exists('users', 'id')],
        ]);

        $task = SharedTask::create([
            'property_id' => $validated['property_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'rrule' => $validated['rrule'] ?? null,
            'next_run_at' => isset($validated['next_run_at'])
                ? Carbon::parse($validated['next_run_at'])
                : null,
            'assignee_user_id' => $validated['assignee_user_id'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Tugas berhasil dibuat.',
                'data' => $task,
            ], 201);
        }

        return redirect()
            ->route('owner.shared-tasks.show', $task)
            ->with('status', 'Tugas berhasil dibuat.');
    }
}
