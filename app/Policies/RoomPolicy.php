<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
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

    public function view(User $user, Room $room): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $room->roomType->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $room->contracts()->where('tenant_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user, RoomType $roomType): bool
    {
        return $user->role === User::ROLE_OWNER && $roomType->property->owner_id === $user->id;
    }

    public function update(User $user, Room $room): bool
    {
        return $user->role === User::ROLE_OWNER && $room->roomType->property->owner_id === $user->id;
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->role === User::ROLE_OWNER && $room->roomType->property->owner_id === $user->id;
    }
}
