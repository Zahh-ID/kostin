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
                'roles' => ['tenant', 'owner', 'admin'],
            ],
            [
                'label' => __('Profile'),
                'route' => 'profile.edit',
                'roles' => ['tenant', 'owner', 'admin'],
            ],
            [
                'label' => __('Chat'),
                'route' => 'chat.index',
                'roles' => ['tenant', 'owner', 'admin'],
            ],
        ];
    }

    public function roleNav(): array
    {
        return match ($this->user?->role) {
            User::ROLE_TENANT => [
                ['label' => __('Tenant Dashboard'), 'route' => 'tenant.dashboard'],
                ['label' => __('Kontrak'), 'route' => 'tenant.contracts.index'],
                ['label' => __('Tagihan'), 'route' => 'tenant.invoices.index'],
                ['label' => __('Wishlist'), 'route' => 'tenant.wishlist.index'],
                ['label' => __('Pencarian Tersimpan'), 'route' => 'tenant.saved-searches.index'],
                ['label' => __('Tiket'), 'route' => 'tenant.tickets.index'],
            ],
            User::ROLE_OWNER => [
                ['label' => __('Owner Dashboard'), 'route' => 'owner.dashboard'],
                ['label' => __('Properti'), 'route' => 'owner.properties.index'],
                ['label' => __('Kontrak'), 'route' => 'owner.contracts.index'],
                ['label' => __('Tugas Bersama'), 'route' => 'owner.shared-tasks.index'],
                ['label' => __('Pembayaran Manual'), 'route' => 'owner.manual-payments.index'],
                ['label' => __('Tiket'), 'route' => 'owner.tickets.index'],
            ],
            User::ROLE_ADMIN => [
                ['label' => __('Admin Dashboard'), 'route' => 'admin.dashboard'],
                ['label' => __('Ticketing'), 'route' => 'admin.tickets.index'],
                ['label' => __('Moderasi Properti'), 'route' => 'admin.moderations.index'],
                ['label' => __('Pengguna'), 'route' => 'admin.users.index'],
                ['label' => __('Pengaturan'), 'route' => 'admin.settings'],
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
                'roles' => ['tenant', 'owner', 'admin'],
            ],
            [
                'label' => __('List Properti Baru'),
                'route' => 'owner.properties.create',
                'roles' => ['owner'],
            ],
            [
                'label' => __('Keluar'),
                'action' => 'logout',
                'roles' => ['tenant', 'owner', 'admin'],
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

<div class="p-3">
    <div class="text-muted text-uppercase small mb-2">{{ __('Navigasi Utama') }}</div>
    <div class="nav nav-pills flex-column mb-3">
        @foreach ($this->primaryNav() as $item)
            @if ((! isset($item['roles']) || in_array($user?->role, $item['roles'] ?? [], true)) && $this->routeExists($item['route'] ?? null))
                <a class="nav-link d-flex justify-content-between align-items-center @if ($this->isActive($item['route'])) active @endif" href="{{ route($item['route']) }}">
                    <span>{{ $item['label'] }}</span>
                    @if (($badges[$item['route']] ?? 0) > 0)
                        <span class="badge bg-primary rounded-pill">{{ $badges[$item['route']] }}</span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>

    @if (! empty($this->roleNav()))
        <div class="text-muted text-uppercase small mb-2">{{ __('Menu :role', ['role' => ucfirst($user?->role ?? '')]) }}</div>
        <div class="nav nav-pills flex-column mb-3">
            @foreach ($this->roleNav() as $item)
                @if ($this->routeExists($item['route'] ?? null))
                    <a class="nav-link d-flex justify-content-between align-items-center @if ($this->isActive($item['route'])) active @endif" href="{{ route($item['route']) }}">
                        <span>{{ $item['label'] }}</span>
                        @if (($badges[$item['route']] ?? 0) > 0)
                            <span class="badge bg-primary rounded-pill">{{ $badges[$item['route']] }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    <div class="text-muted text-uppercase small mb-2">{{ __('Lainnya') }}</div>
    <div class="nav nav-pills flex-column">
        @foreach ($this->supportNav() as $item)
            @php
                $roles = $item['roles'] ?? null;
                $shouldShow = $roles === null || in_array($user?->role, $roles, true);
            @endphp
            @if ($shouldShow)
                @if (isset($item['route']) && $this->routeExists($item['route']))
                    <a class="nav-link d-flex justify-content-between align-items-center @if ($this->isActive($item['route'])) active @endif" href="{{ route($item['route']) }}">
                        <span>{{ $item['label'] }}</span>
                        @if (($badges[$item['route']] ?? 0) > 0)
                            <span class="badge bg-primary rounded-pill">{{ $badges[$item['route']] }}</span>
                        @endif
                    </a>
                @elseif (isset($item['action']) && $item['action'] === 'logout')
                    <form method="POST" action="{{ route('logout') }}" class="w-100">
                        @csrf
                        <button type="submit" class="nav-link text-start border-0 bg-transparent w-100">
                            {{ $item['label'] }}
                        </button>
                    </form>
                @elseif (isset($item['action']))
                    <button type="button" class="nav-link text-start" wire:click="{{ $item['action'] }}">
                        {{ $item['label'] }}
                    </button>
                @endif
            @endif
        @endforeach
    </div>
</div>
