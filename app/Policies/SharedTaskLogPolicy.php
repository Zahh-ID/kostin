<?php

namespace App\Policies;

use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SharedTaskLogPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [User::ROLE_OWNER, User::ROLE_TENANT], true);
    }

    public function view(User $user, SharedTaskLog $log): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $log->sharedTask->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            if ($log->completed_by === $user->id) {
                return true;
            }

            return $log->sharedTask->property->roomTypes()
                ->whereHas('rooms.contracts', fn ($query) => $query->where('tenant_id', $user->id))
                ->exists();
        }

        return false;
    }

    public function create(User $user, SharedTask $task): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $task->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $task->assignee_user_id === $user->id;
        }

        return false;
    }
}
