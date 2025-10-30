<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
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
        return in_array($user->role, [User::ROLE_OWNER, User::ROLE_TENANT, User::ROLE_ADMIN], true);
    }

    public function view(User $user, Property $property): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $this->tenantHasProperty($user, $property);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [User::ROLE_OWNER, User::ROLE_ADMIN], true);
    }

    public function update(User $user, Property $property): bool
    {
        return $user->role === User::ROLE_OWNER && $property->owner_id === $user->id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->role === User::ROLE_OWNER && $property->owner_id === $user->id;
    }

    private function tenantHasProperty(User $user, Property $property): bool
    {
        return $property->roomTypes()->whereHas('rooms.contracts', function ($query) use ($user) {
            $query->where('tenant_id', $user->id);
        })->exists();
    }
}
