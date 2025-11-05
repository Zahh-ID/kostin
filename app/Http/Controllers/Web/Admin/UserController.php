<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->query('role');

        /** @var LengthAwarePaginator $users */
        $users = User::query()
            ->when($role, fn ($query) => $query->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'role' => $role,
            ],
            'availableRoles' => [
                User::ROLE_ADMIN,
                User::ROLE_OWNER,
                User::ROLE_TENANT,
            ],
        ]);
    }

    public function show(User $user): View
    {
        $user->load([
            'properties.roomTypes.rooms',
            'contracts.room.roomType.property',
            'invoices' => fn ($query) => $query->latest('due_date')->limit(10),
        ]);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }
}
