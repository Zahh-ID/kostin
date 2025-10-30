<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
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

    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === User::ROLE_OWNER) {
            return $payment->invoice->contract->room->roomType->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $payment->invoice->contract->tenant_id === $user->id;
        }

        return false;
    }

    public function create(User $user, Invoice $invoice): bool
    {
        if ($invoice->status === 'canceled') {
            return false;
        }

        if ($user->role === User::ROLE_OWNER) {
            return $invoice->contract->room->roomType->property->owner_id === $user->id;
        }

        if ($user->role === User::ROLE_TENANT) {
            return $invoice->contract->tenant_id === $user->id;
        }

        return false;
    }
}
