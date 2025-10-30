<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
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

    public function view(User $user, Contract $contract): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $contract->room->roomType->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $contract->tenant_id === $user->id;
        }

        return false;
    }

    public function create(User $user, Room $room): bool
    {
        return $user->role === User::ROLE_OWNER && $room->roomType->property->owner_id === $user->id;
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->role === User::ROLE_OWNER && $contract->room->roomType->property->owner_id === $user->id;
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->role === User::ROLE_OWNER && $contract->room->roomType->property->owner_id === $user->id;
    }
}
