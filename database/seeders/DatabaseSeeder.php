<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SavedSearch;
use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        $now = Carbon::now();

        $admin = $this->createOrUpdateUser([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'phone' => '081200000000',
        ], User::ROLE_ADMIN, $password);

        $owners = collect([
            ['name' => 'Property Owner', 'email' => 'owner@example.com', 'phone' => '081234567890'],
            ['name' => 'Cozy Host', 'email' => 'owner2@example.com', 'phone' => '081298765432'],
        ])->map(fn (array $data) => $this->createOrUpdateUser($data, User::ROLE_OWNER, $password));

        $tenants = collect([
            ['name' => 'Sample Tenant', 'email' => 'tenant@example.com', 'phone' => '081311223344'],
            ['name' => 'Active Tenant', 'email' => 'tenant2@example.com', 'phone' => '081355667788'],
        ])->map(fn (array $data) => $this->createOrUpdateUser($data, User::ROLE_TENANT, $password));

        $primaryOwner = $owners->first();
        $secondaryOwner = $owners->skip(1)->first();
        $primaryTenant = $tenants->first();
        $secondaryTenant = $tenants->skip(1)->first();

        collect([
            [
                'method' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'PT Kos Kita Indonesia',
                'instructions' => 'Transfer sebelum 22.00 WIB agar verifikasi di hari yang sama.',
                'display_order' => 1,
            ],
            [
                'method' => 'Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'PT Kos Kita Indonesia',
                'instructions' => 'Cantumkan berita transfer: Pembayaran KostIn.',
                'display_order' => 2,
            ],
            [
                'method' => 'BNI',
                'account_number' => '5555666677',
                'account_name' => 'PT Kos Kita Indonesia',
                'instructions' => 'Verifikasi maksimal 1x24 jam kerja.',
                'display_order' => 3,
            ],
            [
                'method' => 'Cash',
                'account_number' => null,
                'account_name' => 'Office Kost Harmoni',
                'instructions' => 'Datang ke kantor manajemen kos: Senin–Jumat 09.00–17.00 WIB.',
                'display_order' => 4,
            ],
        ])->each(function (array $account): void {
            PaymentAccount::updateOrCreate(
                ['method' => $account['method']],
                [
                    'account_number' => $account['account_number'],
                    'account_name' => $account['account_name'],
                    'instructions' => $account['instructions'],
                    'display_order' => $account['display_order'],
                    'is_active' => true,
                ]
            );
        });

        // Approved property with active contract & invoices
        $kostHarmoni = Property::factory()->create([
            'owner_id' => $primaryOwner->id,
            'name' => 'Kost Harmoni',
            'address' => 'Jl. Melati No. 12, Yogyakarta',
            'lat' => -7.7911234,
            'lng' => 110.3654321,
            'rules_text' => "Tidak boleh merokok di dalam kamar.\nTamu lawan jenis hingga pukul 22.00.",
            'photos' => [
                'https://via.placeholder.com/960x640.png?text=Kost+Harmoni+Exterior',
                'https://via.placeholder.com/960x640.png?text=Kamar+Standard',
            ],
            'status' => 'approved',
            'moderated_by' => $admin->id,
            'moderated_at' => $now->copy()->subDays(3),
            'moderation_notes' => 'Properti lulus pemeriksaan kualitas awal.',
        ]);

        $standardType = RoomType::factory()->create([
            'property_id' => $kostHarmoni->id,
            'name' => 'Standard Single',
            'area_m2' => 14,
            'bathroom_type' => 'inside',
            'base_price' => 1500000,
            'deposit' => 500000,
            'facilities_json' => ['wifi' => true, 'ac' => true, 'laundry' => true],
        ]);

        $deluxeType = RoomType::factory()->create([
            'property_id' => $kostHarmoni->id,
            'name' => 'Deluxe Balcony',
            'area_m2' => 18,
            'bathroom_type' => 'inside',
            'base_price' => 1900000,
            'deposit' => 750000,
            'facilities_json' => ['wifi' => true, 'ac' => true, 'water_heater' => true, 'parking' => true],
        ]);

        $roomH101 = Room::factory()->create([
            'room_type_id' => $standardType->id,
            'room_code' => '101',
            'status' => 'occupied',
            'custom_price' => 1500000,
            'description' => 'Kamar standar dengan jendela lebar menghadap taman.',
            'photos_json' => [
                'https://via.placeholder.com/960x640.png?text=Kamar+101',
            ],
        ]);

        Room::factory()->create([
            'room_type_id' => $standardType->id,
            'room_code' => '102',
            'status' => 'available',
            'custom_price' => 1500000,
            'description' => 'Unit menghadap timur dengan cahaya pagi, cocok untuk pekerja remote.',
            'photos_json' => [
                'https://via.placeholder.com/960x640.png?text=Kamar+102',
            ],
        ]);

        Room::factory()->create([
            'room_type_id' => $deluxeType->id,
            'room_code' => '203',
            'status' => 'available',
            'custom_price' => 1950000,
            'description' => 'Kamar deluxe dengan balkon pribadi dan kamar mandi dalam.',
            'photos_json' => [
                'https://via.placeholder.com/960x640.png?text=Kamar+203',
            ],
        ]);

        $contractHarmoni = Contract::factory()->create([
            'tenant_id' => $primaryTenant->id,
            'room_id' => $roomH101->id,
            'start_date' => $now->copy()->subMonths(1)->startOfMonth(),
            'end_date' => null,
            'price_per_month' => 1500000,
            'billing_day' => 5,
            'deposit_amount' => 500000,
            'grace_days' => 3,
            'late_fee_per_day' => 25000,
            'status' => 'active',
        ]);

        $openStart = $now->copy()->startOfMonth();
        $overdueStart = $now->copy()->subMonth()->startOfMonth();

        $openInvoice = Invoice::factory()->create([
            'contract_id' => $contractHarmoni->id,
            'period_month' => $openStart->month,
            'period_year' => $openStart->year,
            'months_count' => 1,
            'coverage_start_month' => $openStart->month,
            'coverage_start_year' => $openStart->year,
            'coverage_end_month' => $openStart->month,
            'coverage_end_year' => $openStart->year,
            'due_date' => $openStart->copy()->day($contractHarmoni->billing_day),
            'amount' => 1500000,
            'late_fee' => 0,
            'total' => 1500000,
            'status' => 'unpaid',
            'qris_payload' => ['qr_string' => '000201010211...KOSTIN...'],
        ]);

        $overdueInvoice = Invoice::factory()->create([
            'contract_id' => $contractHarmoni->id,
            'period_month' => $overdueStart->month,
            'period_year' => $overdueStart->year,
            'months_count' => 1,
            'coverage_start_month' => $overdueStart->month,
            'coverage_start_year' => $overdueStart->year,
            'coverage_end_month' => $overdueStart->month,
            'coverage_end_year' => $overdueStart->year,
            'due_date' => $overdueStart->copy()->day($contractHarmoni->billing_day),
            'amount' => 1500000,
            'late_fee' => 50000,
            'total' => 1550000,
            'status' => 'overdue',
            'qris_payload' => ['qr_string' => '000201010211...KOSTIN-PAST...'],
        ]);

        Payment::updateOrCreate(
            [
                'invoice_id' => $overdueInvoice->id,
                'payment_type' => 'qris',
            ],
            [
                'user_id' => $primaryTenant->id,
                'submitted_by' => null,
                'order_id' => 'ORDER-'.$overdueInvoice->id,
                'manual_method' => null,
                'proof_path' => null,
                'proof_filename' => null,
                'notes' => null,
                'amount' => $overdueInvoice->total,
                'status' => 'success',
                'settlement_time' => $now->copy()->subDays(3),
                'verified_by' => $admin->id,
                'verified_at' => $now->copy()->subDays(3)->addHour(),
                'rejection_reason' => null,
                'raw_webhook_json' => [
                    'signature_key' => 'demo-signature',
                    'settlement_time' => $now->copy()->subDays(3)->toIso8601String(),
                ],
            ]
        );

        $manualPayment = Payment::updateOrCreate(
            [
                'invoice_id' => $openInvoice->id,
                'payment_type' => 'manual_bank_transfer',
            ],
            [
                'user_id' => $primaryTenant->id,
                'submitted_by' => $primaryTenant->id,
                'order_id' => 'MANUAL-'.$openInvoice->id,
                'manual_method' => 'BCA',
                'proof_path' => 'manual-payments/'.$openInvoice->id.'-bukti.jpg',
                'proof_filename' => 'bukti-transfer-november.jpg',
                'notes' => 'Transfer manual melalui BCA Mobile pada '.$now->copy()->subDay()->format('d M Y H:i'),
                'amount' => $openInvoice->total,
                'status' => 'waiting_verification',
                'settlement_time' => null,
                'verified_by' => null,
                'verified_at' => null,
                'rejection_reason' => null,
                'raw_webhook_json' => null,
            ]
        );

        $cleanTask = SharedTask::factory()->create([
            'property_id' => $kostHarmoni->id,
            'title' => 'Kebersihan Koridor',
            'description' => 'Pembersihan koridor lantai 1 dan pengecekan tempat sampah.',
            'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
            'assignee_user_id' => $primaryOwner->id,
            'next_run_at' => $now->copy()->addDays(2),
        ]);

        SharedTaskLog::factory()->create([
            'shared_task_id' => $cleanTask->id,
            'completed_by' => $primaryTenant->id,
            'run_at' => $now->copy()->subDays(5),
            'photo_url' => 'https://via.placeholder.com/400x280.png?text=Tugas+Koridor',
            'note' => 'Koridor sudah dipel dan lampu diganti.',
        ]);

        SharedTaskLog::factory()->create([
            'shared_task_id' => $cleanTask->id,
            'completed_by' => $primaryOwner->id,
            'run_at' => $now->copy()->subDays(12),
            'photo_url' => null,
            'note' => 'Inspeksi mingguan berjalan lancar.',
        ]);

        // Pending property to showcase moderation
        Property::factory()->create([
            'owner_id' => $primaryOwner->id,
            'name' => 'Kost Sunrise',
            'address' => 'Jl. Kenanga No. 3, Sleman',
            'status' => 'pending',
            'photos' => [
                'https://via.placeholder.com/960x640.png?text=Kost+Sunrise',
            ],
            'moderated_by' => null,
            'moderated_at' => null,
            'moderation_notes' => null,
        ]);

        // Secondary owner property with paid invoices
        $skyResidence = Property::factory()->create([
            'owner_id' => $secondaryOwner->id,
            'name' => 'Sky Residence',
            'address' => 'Jl. Sudirman No. 8, Bandung',
            'lat' => -6.9054321,
            'lng' => 107.6131234,
            'rules_text' => 'Tamu wajib lapor resepsionis. Dilarang membawa hewan peliharaan.',
            'photos' => [
                'https://via.placeholder.com/960x640.png?text=Sky+Residence',
            ],
            'status' => 'approved',
            'moderated_by' => $admin->id,
            'moderated_at' => $now->copy()->subDays(5),
            'moderation_notes' => 'Disetujui oleh admin untuk tayang publik.',
        ]);

        $suiteType = RoomType::factory()->create([
            'property_id' => $skyResidence->id,
            'name' => 'Sky Suite',
            'area_m2' => 22,
            'bathroom_type' => 'inside',
            'base_price' => 2200000,
            'deposit' => 1000000,
            'facilities_json' => ['wifi' => true, 'ac' => true, 'water_heater' => true, 'laundry' => true],
        ]);

        $roomS301 = Room::factory()->create([
            'room_type_id' => $suiteType->id,
            'room_code' => '301',
            'status' => 'occupied',
            'custom_price' => 2300000,
        ]);

        Room::factory()->create([
            'room_type_id' => $suiteType->id,
            'room_code' => '302',
            'status' => 'available',
            'custom_price' => null,
        ]);

        WishlistItem::firstOrCreate(
            [
                'user_id' => $primaryTenant->id,
                'property_id' => $kostHarmoni->id,
            ]
        );

        WishlistItem::firstOrCreate(
            [
                'user_id' => $primaryTenant->id,
                'property_id' => $skyResidence->id,
            ]
        );

        WishlistItem::firstOrCreate(
            [
                'user_id' => $secondaryTenant->id,
                'property_id' => $skyResidence->id,
            ]
        );

        SavedSearch::updateOrCreate(
            [
                'user_id' => $primaryTenant->id,
                'name' => 'Kos Dekat Kampus',
            ],
            [
                'filters' => [
                    'search' => 'kampus',
                    'city' => 'Bogor',
                    'type' => 'campur',
                    'minPrice' => 1000000,
                    'maxPrice' => 2000000,
                    'facilities' => ['wifi', 'ac', 'laundry'],
                ],
                'notification_enabled' => true,
                'last_notified_at' => $now->copy()->subDays(2),
            ]
        );

        SavedSearch::updateOrCreate(
            [
                'user_id' => $secondaryTenant->id,
                'name' => 'Kos Eksklusif Bandung',
            ],
            [
                'filters' => [
                    'search' => 'eksklusif',
                    'city' => 'Bandung',
                    'type' => 'putra',
                    'minPrice' => 1500000,
                    'maxPrice' => 3000000,
                    'facilities' => ['wifi', 'ac', 'water_heater'],
                ],
                'notification_enabled' => false,
                'last_notified_at' => null,
            ]
        );

        $paymentTicket = Ticket::updateOrCreate(
            [
                'ticket_code' => 'TCK-PAY-001',
            ],
            [
                'reporter_id' => $primaryTenant->id,
                'assignee_id' => $admin->id,
                'subject' => 'Verifikasi pembayaran kos bulan November',
                'description' => 'Halo admin, saya sudah transfer melalui BCA dan mengunggah bukti pembayaran.',
                'category' => 'payment',
                'priority' => 'high',
                'status' => Ticket::STATUS_IN_REVIEW,
                'related_type' => Invoice::class,
                'related_id' => $openInvoice->id,
                'tags' => ['manual_payment', 'invoice'],
                'sla_minutes' => 1440,
                'closed_at' => null,
                'escalated_at' => null,
            ]
        );

        TicketComment::firstOrCreate(
            [
                'ticket_id' => $paymentTicket->id,
                'user_id' => $primaryTenant->id,
                'body' => 'Saya sudah upload bukti transfer BCA. Mohon bantu verifikasinya.',
            ],
            [
                'attachments' => [$manualPayment->proof_path],
            ]
        );

        TicketComment::firstOrCreate(
            [
                'ticket_id' => $paymentTicket->id,
                'user_id' => $admin->id,
                'body' => 'Terima kasih, kami sedang memverifikasi bukti pembayaran Anda.',
            ],
            [
                'attachments' => null,
            ]
        );

        TicketEvent::updateOrCreate(
            [
                'ticket_id' => $paymentTicket->id,
                'event_type' => 'created',
            ],
            [
                'user_id' => $primaryTenant->id,
                'payload' => [
                    'message' => 'Ticket created by tenant',
                ],
            ]
        );

        TicketEvent::updateOrCreate(
            [
                'ticket_id' => $paymentTicket->id,
                'event_type' => 'status_changed',
            ],
            [
                'user_id' => $admin->id,
                'payload' => [
                    'from' => Ticket::STATUS_OPEN,
                    'to' => Ticket::STATUS_IN_REVIEW,
                    'note' => 'Admin memulai proses verifikasi pembayaran.',
                ],
            ]
        );

        $maintenanceTicket = Ticket::updateOrCreate(
            [
                'ticket_code' => 'TCK-OPS-001',
            ],
            [
                'reporter_id' => $secondaryTenant?->id ?? $primaryTenant->id,
                'assignee_id' => $secondaryOwner->id,
                'subject' => 'AC kamar 301 tidak dingin',
                'description' => 'Sejak kemarin malam AC kamar 301 tidak dingin. Mohon dicek.',
                'category' => 'technical',
                'priority' => 'medium',
                'status' => Ticket::STATUS_RESOLVED,
                'related_type' => Room::class,
                'related_id' => $roomS301->id,
                'tags' => ['maintenance', 'ac'],
                'sla_minutes' => 2880,
                'closed_at' => $now->copy()->subDays(1),
                'escalated_at' => null,
            ]
        );

        TicketComment::firstOrCreate(
            [
                'ticket_id' => $maintenanceTicket->id,
                'user_id' => $secondaryOwner->id,
                'body' => 'Teknisi telah membersihkan filter AC dan menambah freon. Silakan cek kembali ya.',
            ],
            [
                'attachments' => ['https://via.placeholder.com/640x360.png?text=Service+AC'],
            ]
        );

        TicketEvent::updateOrCreate(
            [
                'ticket_id' => $maintenanceTicket->id,
                'event_type' => 'created',
            ],
            [
                'user_id' => $secondaryTenant?->id ?? $primaryTenant->id,
                'payload' => [
                    'message' => 'Tenant melaporkan keluhan AC.',
                ],
            ]
        );

        TicketEvent::updateOrCreate(
            [
                'ticket_id' => $maintenanceTicket->id,
                'event_type' => 'resolved',
            ],
            [
                'user_id' => $secondaryOwner->id,
                'payload' => [
                    'note' => 'Teknisi menyelesaikan pengecekan AC kamar 301.',
                    'closed_at' => $now->copy()->subDays(1)->toIso8601String(),
                ],
            ]
        );

        $contractSky = Contract::factory()->create([
            'tenant_id' => $secondaryTenant->id,
            'room_id' => $roomS301->id,
            'start_date' => $now->copy()->startOfMonth(),
            'end_date' => null,
            'price_per_month' => 2300000,
            'billing_day' => 10,
            'deposit_amount' => 1000000,
            'grace_days' => 2,
            'late_fee_per_day' => 35000,
            'status' => 'active',
        ]);

        $paidInvoice = Invoice::factory()->create([
            'contract_id' => $contractSky->id,
            'period_month' => $now->month,
            'period_year' => $now->year,
            'due_date' => $now->copy()->startOfMonth()->day($contractSky->billing_day),
            'amount' => 2300000,
            'late_fee' => 0,
            'total' => 2300000,
            'status' => 'paid',
            'qris_payload' => ['qr_string' => '000201010211...SKYRESIDENCE...'],
        ]);

        $unpaidInvoice = Invoice::factory()->create([
            'contract_id' => $contractSky->id,
            'period_month' => $now->copy()->addMonth()->month,
            'period_year' => $now->copy()->addMonth()->year,
            'due_date' => $now->copy()->addMonth()->startOfMonth()->day($contractSky->billing_day),
            'amount' => 2300000,
            'late_fee' => 0,
            'total' => 2300000,
            'status' => 'unpaid',
            'qris_payload' => null,
        ]);

        Payment::updateOrCreate(
            [
                'invoice_id' => $paidInvoice->id,
                'payment_type' => 'qris',
            ],
            [
                'user_id' => $secondaryTenant->id,
                'submitted_by' => null,
                'order_id' => 'ORDER-'.$paidInvoice->id,
                'manual_method' => null,
                'proof_path' => null,
                'proof_filename' => null,
                'notes' => null,
                'amount' => 2300000,
                'status' => 'success',
                'settlement_time' => $now->copy()->startOfMonth()->day($contractSky->billing_day)->addHours(2),
                'verified_by' => $admin->id,
                'verified_at' => $now->copy()->startOfMonth()->day($contractSky->billing_day)->addHours(3),
                'rejection_reason' => null,
                'raw_webhook_json' => [
                    'signature_key' => 'demo-signature',
                    'settlement_time' => $now->copy()->startOfMonth()->day($contractSky->billing_day)->addHours(2)->toIso8601String(),
                ],
            ]
        );

        $maintenanceTask = SharedTask::factory()->create([
            'property_id' => $skyResidence->id,
            'title' => 'Pemeriksaan AC Bulanan',
            'description' => 'Cek filter AC seluruh Sky Suite dan catat kebutuhan freon.',
            'rrule' => 'FREQ=MONTHLY;BYDAY=MO;BYSETPOS=1',
            'assignee_user_id' => $secondaryOwner->id,
            'next_run_at' => $now->copy()->addDays(5),
        ]);

        SharedTaskLog::factory()->create([
            'shared_task_id' => $maintenanceTask->id,
            'completed_by' => $secondaryOwner->id,
            'run_at' => $now->copy()->subDays(10),
            'photo_url' => 'https://via.placeholder.com/400x280.png?text=Service+AC',
            'note' => 'Filter AC kamar 301 dibersihkan, tidak perlu penggantian.',
        ]);

        $chatParticipants = $owners->pluck('id')
            ->merge($tenants->pluck('id'))
            ->push($admin->id)
            ->unique();

        $globalConversation = Conversation::updateOrCreate(
            ['title' => 'Forum KostIn'],
            ['is_group' => true, 'metadata' => ['description' => 'Ruang diskusi antara admin, owner, dan tenant.']]
        );

        $globalConversation->participants()->sync(
            $chatParticipants->mapWithKeys(fn (int $userId) => [
                $userId => ['last_read_at' => $now, 'role' => 'member'],
            ])->all()
        );

        $seedMessages = [
            ['user_id' => $admin->id, 'body' => 'Selamat datang di forum KostIn. Gunakan ruang ini untuk koordinasi harian.'],
            ['user_id' => $owners->first()->id, 'body' => 'Terima kasih! Kami siap menerima masukan tenant agar layanan makin nyaman.'],
            ['user_id' => $tenants->first()->id, 'body' => 'Halo semuanya, izin bertanya soal jadwal pembersihan koridor minggu ini?'],
            ['user_id' => $secondaryOwner->id, 'body' => 'Koridor akan dibersihkan besok pagi pukul 09.00, seperti biasa.'],
            ['user_id' => $tenants->skip(1)->first()->id ?? $tenants->first()->id, 'body' => 'Noted, terima kasih informasinya.'],
        ];

        foreach ($seedMessages as $index => $messageData) {
            Message::updateOrCreate(
                [
                    'conversation_id' => $globalConversation->id,
                    'user_id' => $messageData['user_id'],
                    'body' => $messageData['body'],
                ],
                [
                    'created_at' => $now->copy()->subMinutes(15 - ($index * 3)),
                    'updated_at' => $now->copy()->subMinutes(15 - ($index * 3)),
                ]
            );
        }

        $globalConversation->touch();

        AuditLog::factory()->create([
            'user_id' => $admin->id,
            'action' => 'seed',
            'entity' => 'system',
            'entity_id' => 1,
            'meta_json' => ['message' => 'Initial demo data created.'],
            'created_at' => $now,
        ]);

        AuditLog::factory()->create([
            'user_id' => $primaryOwner->id,
            'action' => 'property.update',
            'entity' => 'property',
            'entity_id' => $kostHarmoni->id,
            'meta_json' => ['status' => 'approved', 'changed_by' => $primaryOwner->email],
            'created_at' => $now->copy()->subDay(),
        ]);
    }

    private function createOrUpdateUser(array $data, string $role, string $password): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'role' => $role,
                'password' => $password,
                'email_verified_at' => now(),
            ]
        );
    }
}
