<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomTypePolicy
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

    public function view(User $user, RoomType $roomType): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $roomType->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $roomType->property->roomTypes()
                ->where('id', $roomType->id)
                ->whereHas('rooms.contracts', fn ($query) => $query->where('tenant_id', $user->id))
                ->exists();
        }

        return false;
    }

    public function create(User $user, Property $property): bool
    {
        return $user->role === User::ROLE_OWNER && $property->owner_id === $user->id;
    }

    public function update(User $user, RoomType $roomType): bool
    {
        return $user->role === User::ROLE_OWNER && $roomType->property->owner_id === $user->id;
    }

    public function delete(User $user, RoomType $roomType): bool
    {
        return $user->role === User::ROLE_OWNER && $roomType->property->owner_id === $user->id;
    }
}
