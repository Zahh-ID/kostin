<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirect authenticated users to their role-specific dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $cards = collect();
        $actions = collect();

        switch ($user->role) {
            case 'tenant':
                $cards = collect([
                    [
                        'label' => __('Kontrak Aktif'),
                        'value' => Contract::query()
                            ->where('tenant_id', $user->id)
                            ->where('status', Contract::STATUS_ACTIVE)
                            ->count(),
                        'description' => __('Kontrak berjalan saat ini.'),
                    ],
                    [
                        'label' => __('Tagihan Belum Lunas'),
                        'value' => Invoice::query()
                            ->where('status', '!=', 'paid')
                            ->whereHas('contract', fn ($query) => $query->where('tenant_id', $user->id))
                            ->count(),
                        'description' => __('Jumlah tagihan perlu ditindaklanjuti.'),
                    ],
                    [
                        'label' => __('Total Tertunggak'),
                        'value' => 'Rp'.number_format(Invoice::query()
                            ->whereIn('status', ['unpaid', 'overdue'])
                            ->whereHas('contract', fn ($query) => $query->where('tenant_id', $user->id))
                            ->sum('total'), 0, ',', '.'),
                        'description' => __('Ringkasan nominal sebelum denda tambahan.'),
                    ],
                    [
                        'label' => __('Tiket Support Aktif'),
                        'value' => Ticket::query()
                            ->where('reporter_id', $user->id)
                            ->whereIn('status', [
                                Ticket::STATUS_OPEN,
                                Ticket::STATUS_IN_REVIEW,
                                Ticket::STATUS_ESCALATED,
                            ])
                            ->count(),
                        'description' => __('Laporan yang sedang diproses oleh tim.'),
                    ],
                ]);

                $actions = collect([
                    [
                        'label' => __('Lihat Tagihan'),
                        'description' => __('Kelola pembayaran dan unggah bukti secara langsung.'),
                        'route' => 'tenant.invoices.index',
                    ],
                    [
                        'label' => __('Ajukan Tiket'),
                        'description' => __('Laporkan kendala agar pemilik/admin dapat membantu.'),
                        'route' => 'tenant.tickets.index',
                    ],
                ]);
                break;

            case 'owner':
                $availableRooms = Room::query()
                    ->whereHas('roomType.property', fn ($query) => $query->where('owner_id', $user->id))
                    ->where('status', 'available')
                    ->count();

                $cards = collect([
                    [
                        'label' => __('Total Properti'),
                        'value' => Property::query()->where('owner_id', $user->id)->count(),
                        'description' => __('Jumlah listing milik Anda.'),
                    ],
                    [
                        'label' => __('Kamar Tersedia'),
                        'value' => $availableRooms,
                        'description' => __('Unit siap disewakan.'),
                    ],
                    [
                        'label' => __('Tagihan Tertunggak'),
                        'value' => Invoice::query()
                            ->whereIn('status', ['unpaid', 'overdue'])
                            ->whereHas('contract.room.roomType.property', fn ($query) => $query->where('owner_id', $user->id))
                            ->count(),
                        'description' => __('Monitor tagihan tenant yang belum lunas.'),
                    ],
                    [
                        'label' => __('Tugas Terjadwal'),
                        'value' => $user->sharedTasks()->count(),
                        'description' => __('Tugas utilitas & operasional yang perlu dijalankan.'),
                    ],
                ]);

                $actions = collect([
                    [
                        'label' => __('Kelola Properti'),
                        'description' => __('Perbarui data kamar, harga, dan ketersediaan.'),
                        'route' => 'owner.properties.index',
                    ],
                    [
                        'label' => __('Pantau Tagihan'),
                        'description' => __('Verifikasi pembayaran manual dan invoice tenant.'),
                        'route' => 'owner.manual-payments.index',
                    ],
                ]);
                break;

            default:
                $cards = collect([
                    [
                        'label' => __('Properti Menunggu Moderasi'),
                        'value' => Property::where('status', 'pending')->count(),
                        'description' => __('Listing yang memerlukan tindakan admin.'),
                    ],
                    [
                        'label' => __('Tiket Support Terbuka'),
                        'value' => Ticket::whereIn('status', [
                            Ticket::STATUS_OPEN,
                            Ticket::STATUS_IN_REVIEW,
                            Ticket::STATUS_ESCALATED,
                        ])->count(),
                        'description' => __('Isu tenant/owner yang belum terselesaikan.'),
                    ],
                    [
                        'label' => __('Pengguna Terdaftar'),
                        'value' => User::count(),
                        'description' => __('Total akun aktif dalam platform.'),
                    ],
                    [
                        'label' => __('Kontrak Aktif'),
                        'value' => Contract::where('status', Contract::STATUS_ACTIVE)->count(),
                        'description' => __('Kontrak sewa berjalan saat ini.'),
                    ],
                ]);

                $actions = collect([
                    [
                        'label' => __('Moderasi Properti'),
                        'description' => __('Validasi listing baru sebelum dipublikasikan.'),
                        'route' => 'admin.moderations.index',
                    ],
                    [
                        'label' => __('Kelola Tiket Support'),
                        'description' => __('Atur penugasan dan tindak lanjut tiket.'),
                        'route' => 'admin.tickets.index',
                    ],
                ]);
        }

        $actions = $actions->filter(fn ($action) => isset($action['route']) ? Route::has($action['route']) : true);

        return view('dashboard', [
            'cards' => $cards,
            'actions' => $actions,
        ]);
    }
}
