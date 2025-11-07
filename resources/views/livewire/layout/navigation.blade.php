<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public ?User $user = null;

    public array $badges = [];

    public function mount(): void
    {
        $this->user = Auth::user();

        if ($this->user === null) {
            return;
        }

        $role = $this->user->role;

        if ($role === User::ROLE_TENANT) {
            $this->badges['tenant.invoices.index'] = Invoice::query()
                ->whereIn('status', ['unpaid', 'overdue'])
                ->whereHas('contract', fn ($query) => $query->where('tenant_id', $this->user->id))
                ->count();

            $this->badges['tenant.tickets.index'] = Ticket::query()
                ->where('reporter_id', $this->user->id)
                ->whereIn('status', [
                    Ticket::STATUS_OPEN,
                    Ticket::STATUS_IN_REVIEW,
                    Ticket::STATUS_ESCALATED,
                ])
                ->count();
        }

        if ($role === User::ROLE_OWNER) {
            $this->badges['owner.manual-payments.index'] = Payment::query()
                ->where('payment_type', 'manual_bank_transfer')
                ->where('status', 'waiting_verification')
                ->whereHas('invoice.contract.room.roomType.property', fn ($query) => $query->where('owner_id', $this->user->id))
                ->count();

            $this->badges['owner.tickets.index'] = Ticket::query()
                ->where('assignee_id', $this->user->id)
                ->whereIn('status', [
                    Ticket::STATUS_OPEN,
                    Ticket::STATUS_IN_REVIEW,
                    Ticket::STATUS_ESCALATED,
                ])
                ->count();
        }

        if ($role === User::ROLE_ADMIN) {
            $this->badges['admin.tickets.index'] = Ticket::query()
                ->whereIn('status', [
                    Ticket::STATUS_OPEN,
                    Ticket::STATUS_IN_REVIEW,
                    Ticket::STATUS_ESCALATED,
                ])
                ->count();

            $this->badges['admin.moderations.index'] = Property::query()
                ->where('status', 'pending')
                ->count();
        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }

    public function primaryNav(): array
    {
        return [
            [
                'label' => __('Dashboard'),
                'route' => 'dashboard',
                'icon' => 'bi-house-door',
                'roles' => ['tenant', 'owner', 'admin'],
            ],
            [
                'label' => __('Chat'),
                'route' => 'chat.index',
                'icon' => 'bi-chat',
                'roles' => ['tenant', 'owner', 'admin'],
            ],
        ];
    }

    public function roleNav(): array
    {
        return match ($this->user?->role) {
            User::ROLE_TENANT => [
                ['label' => __('Tenant Dashboard'), 'route' => 'tenant.dashboard', 'icon' => 'bi-speedometer2'],
                ['label' => __('Kontrak'), 'route' => 'tenant.contracts.index', 'icon' => 'bi-file-earmark-text'],
                ['label' => __('Tagihan'), 'route' => 'tenant.invoices.index', 'icon' => 'bi-receipt'],
                ['label' => __('Pengajuan Kontrak'), 'route' => 'tenant.applications.index', 'icon' => 'bi-clipboard-check'],
                ['label' => __('Wishlist'), 'route' => 'tenant.wishlist.index', 'icon' => 'bi-heart'],
                ['label' => __('Pencarian Tersimpan'), 'route' => 'tenant.saved-searches.index', 'icon' => 'bi-search-heart'],
                ['label' => __('Tiket'), 'route' => 'tenant.tickets.index', 'icon' => 'bi-ticket'],
            ],
            User::ROLE_OWNER => [
                ['label' => __('Owner Dashboard'), 'route' => 'owner.dashboard', 'icon' => 'bi-speedometer2'],
                ['label' => __('Properti'), 'route' => 'owner.properties.index', 'icon' => 'bi-building'],
                ['label' => __('Kontrak'), 'route' => 'owner.contracts.index', 'icon' => 'bi-file-earmark-text'],
                ['label' => __('Pengajuan Tenant'), 'route' => 'owner.applications.index', 'icon' => 'bi-clipboard-data'],
                ['label' => __('Tugas Bersama'), 'route' => 'owner.shared-tasks.index', 'icon' => 'bi-list-check'],
                ['label' => __('Pembayaran Manual'), 'route' => 'owner.manual-payments.index', 'icon' => 'bi-wallet2'],
                ['label' => __('Tiket'), 'route' => 'owner.tickets.index', 'icon' => 'bi-ticket'],
            ],
            User::ROLE_ADMIN => [
                ['label' => __('Admin Dashboard'), 'route' => 'admin.dashboard', 'icon' => 'bi-speedometer2'],
                ['label' => __('Ticketing'), 'route' => 'admin.tickets.index', 'icon' => 'bi-ticket'],
                ['label' => __('Moderasi Properti'), 'route' => 'admin.moderations.index', 'icon' => 'bi-patch-check'],
                ['label' => __('Pengguna'), 'route' => 'admin.users.index', 'icon' => 'bi-people'],
                ['label' => __('Pengaturan'), 'route' => 'admin.settings', 'icon' => 'bi-gear'],
            ],
            default => [],
        };
    }

    public function supportNav(): array
    {
        return [
            [
                'label' => __('Notifikasi'),
                'route' => 'settings.notifications',
                'icon' => 'bi-bell',
                'roles' => ['tenant', 'owner', 'admin'],
            ],
            [
                'label' => __('List Properti Baru'),
                'route' => 'owner.properties.create',
                'icon' => 'bi-plus-circle',
                'roles' => ['owner'],
            ],
        ];
    }

    public function routeExists(?string $route): bool
    {
        return $route !== null && Route::has($route);
    }

    public function isActive(?string $route): bool
    {
        return $this->routeExists($route) && request()->routeIs($route);
    }
}; ?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-light">
    <div class="d-flex align-items-center justify-content-between">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <span class="fs-4">KostIn</span>
        </a>
        <button id="sidebar-toggler" class="btn">
            <i class="bi bi-list"></i>
        </button>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        @foreach ($this->primaryNav() as $item)
            @if ((! isset($item['roles']) || in_array($user?->role, $item['roles'] ?? [], true)) && $this->routeExists($item['route'] ?? null))
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center @if ($this->isActive($item['route'])) active @else link-dark @endif" href="{{ route($item['route']) }}">
                        <i class="bi {{ $item['icon'] }} me-2"></i>
                        <span>{{ $item['label'] }}</span>
                        @if (($badges[$item['route']] ?? 0) > 0)
                            <span class="badge bg-primary rounded-pill ms-auto">{{ $badges[$item['route']] }}</span>
                        @endif
                    </a>
                </li>
            @endif
        @endforeach

        @if (! empty($this->roleNav()))
            <hr>
            @foreach ($this->roleNav() as $item)
                @if ($this->routeExists($item['route'] ?? null))
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center @if ($this->isActive($item['route'])) active @else link-dark @endif" href="{{ route($item['route']) }}">
                            <i class="bi {{ $item['icon'] }} me-2"></i>
                            <span>{{ $item['label'] }}</span>
                            @if (($badges[$item['route']] ?? 0) > 0)
                                <span class="badge bg-primary rounded-pill ms-auto">{{ $badges[$item['route']] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        <hr>
        @foreach ($this->supportNav() as $item)
            @php
                $roles = $item['roles'] ?? null;
                $shouldShow = $roles === null || in_array($user?->role, $roles, true);
            @endphp
            @if ($shouldShow)
                @if (isset($item['route']) && $this->routeExists($item['route']))
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center @if ($this->isActive($item['route'])) active @else link-dark @endif" href="{{ route($item['route']) }}">
                            <i class="bi {{ $item['icon'] }} me-2"></i>
                            <span>{{ $item['label'] }}</span>
                            @if (($badges[$item['route']] ?? 0) > 0)
                                <span class="badge bg-primary rounded-pill ms-auto">{{ $badges[$item['route']] }}</span>
                            @endif
                        </a>
                    </li>
                @endif
            @endif
        @endforeach
    </ul>
    <hr>
    <div class="dropdown user-dropdown" wire:ignore>
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $user?->avatar_url ?? 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user?->email ?? 'guest@example.com'))) }}" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>{{ $user?->name ?? __('Pengguna') }}</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2" style="z-index: 1050;">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Sign out</button>
                </form>
            </li>
        </ul>
    </div>
</div>
