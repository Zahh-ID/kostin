<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BulkDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding bulk demo data...');

        Storage::disk('public')->makeDirectory('seed/properties');
        Storage::disk('public')->makeDirectory('seed/rooms');

        $exteriorPool = [
            'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1470246973918-29a93221c455?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1468476584508-3e602efe6edd?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1501045661006-fcebe0257c3f?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1508854401524-49fcb50b0a46?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1430285561322-7808604715df?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1465805139202-a644e217f00a?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1501183638710-841dd1904471?auto=format&fit=crop&w=1400&q=80',
        ];
        $roomPool = [
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505693415763-3ed5e04ba4cd?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1484156818044-c040038b0710?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505691723518-36a5ac3be353?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505692794403-34d4982c9803?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1444418776043-4c46d1cd7e1d?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505692069463-7e3409e70f0b?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1400&q=80&sat=-80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1400&q=80&blur=70',
        ];
        $exteriorCursor = 0;
        $roomCursor = 0;

        $admins = User::factory()->admin()->count(2)->create();
        $owners = User::factory()->owner()->count(8)->create();
        $tenants = User::factory()->tenant()->count(30)->create();

        $admin = $admins->first();

        $propertyCount = 22;
        $tenantPointer = 0;
        $ownersCount = $owners->count();

        Collection::times($propertyCount, function (int $i) use (&$tenantPointer, $tenants, $owners, $ownersCount, $admin, $exteriorPool, &$exteriorCursor, $roomPool, &$roomCursor): void {
            $ownerIndex = ($i - 1) % $ownersCount;
            $owner = $owners[$ownerIndex];

            $property = Property::factory()->create([
                'owner_id' => $owner->id,
                'status' => $i % 6 === 0 ? 'pending' : ($i % 5 === 0 ? 'rejected' : 'approved'),
                'photos' => [],
            ]);

            $photos = $this->samplePhotos($exteriorPool, $exteriorCursor, 3, 'seed/properties', "property-{$property->id}");
            $property->update(['photos' => $photos]);

            $roomTypes = RoomType::factory()->count(2)->create([
                'property_id' => $property->id,
            ]);

            $roomTypes->each(function (RoomType $roomType, int $index) use ($roomPool, &$roomCursor): void {
                Collection::times(3, function (int $roomIndex) use ($roomType, $index, $roomPool, &$roomCursor): void {
                    $room = Room::query()->create([
                        'room_type_id' => $roomType->id,
                        'status' => $index === 0 ? 'available' : 'occupied',
                        'room_code' => 'RT'.$roomType->id.'-'.$roomIndex,
                        'custom_price' => $roomType->base_price,
                        'description' => 'Kamar '.$roomType->name.' #'.$roomIndex,
                        'photos_json' => [],
                    ]);

                    $roomPhoto = $this->samplePhotos($roomPool, $roomCursor, 1, 'seed/rooms', "room-{$room->id}");
                    $room->update(['photos_json' => $roomPhoto]);
                });
            });

            $rooms = $property->roomTypes()->with('rooms')->get()->flatMap(fn (RoomType $rt) => $rt->rooms);
            $tenant = $tenants[$tenantPointer % $tenants->count()];
            $tenantPointer++;

            $roomForContract = $rooms->first() ?? Room::factory()->create(['room_type_id' => $roomTypes->first()->id]);

            $contract = Contract::factory()->create([
                'tenant_id' => $tenant->id,
                'room_id' => $roomForContract->id,
                'status' => 'active',
            ]);

            $startPeriod = now()->subMonths(1);

            Collection::times(3, function (int $invoiceIndex) use ($contract, $startPeriod): void {
                $period = $startPeriod->copy()->addMonths($invoiceIndex - 1);

                $invoice = Invoice::factory()->create([
                    'contract_id' => $contract->id,
                    'period_month' => $period->month,
                    'period_year' => $period->year,
                    'coverage_start_month' => $period->month,
                    'coverage_start_year' => $period->year,
                    'coverage_end_month' => $period->month,
                    'coverage_end_year' => $period->year,
                    'due_date' => $period->copy()->day($contract->billing_day ?? 5),
                ]);

                Payment::factory()->create([
                    'invoice_id' => $invoice->id,
                    'status' => $invoiceIndex % 2 === 1 ? 'success' : 'pending',
                    'amount' => $invoice->total,
                ]);
            });

            $conversation = Conversation::create([
                'title' => "{$property->name} Channel",
                'is_group' => true,
                'metadata' => ['property_id' => $property->id],
            ]);

            $conversation->participants()->attach($admin->id, ['role' => 'admin']);
            $conversation->participants()->attach($owner->id, ['role' => 'owner']);
            $conversation->participants()->attach($tenant->id, ['role' => 'tenant']);

            $participants = [$admin, $owner, $tenant];
            Collection::times(5, function (int $idx) use ($conversation, $participants): void {
                $sender = $participants[$idx % count($participants)];
                Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $sender->id,
                    'body' => match ($idx % 3) {
                        0 => 'Halo, status properti sudah dicek?',
                        1 => 'Kontrak dan pembayaran sedang diproses.',
                        default => 'Baik, kami update setelah verifikasi selesai.',
                    },
                ]);
            });

            $ticket = Ticket::create([
                'ticket_code' => 'TCK-PROP-'.$property->id,
                'reporter_id' => $tenant->id,
                'assignee_id' => $owner->id,
                'subject' => 'Keluhan fasilitas di '.$property->name,
                'description' => 'Tenant melaporkan kendala awal (wifi/AC/kebersihan) untuk properti ini.',
                'category' => $i % 2 === 0 ? 'technical' : 'payment',
                'priority' => $i % 3 === 0 ? 'high' : 'medium',
                'status' => Ticket::STATUS_IN_REVIEW,
                'related_type' => Property::class,
                'related_id' => $property->id,
                'tags' => ['seed', 'maintenance'],
                'sla_minutes' => 1440,
                'closed_at' => null,
                'escalated_at' => null,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tenant->id,
                'body' => 'Mohon bantuan, ada kendala pada properti ini. Bisa dibantu cek?',
                'attachments' => [],
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $owner->id,
                'body' => 'Catatan diterima, kami jadwalkan pemeriksaan hari ini.',
                'attachments' => [],
            ]);
        });

        $this->command?->info('Bulk demo seeding completed.');
    }

    /**
     * @return array<int, string>
     */
    private function samplePhotos(array $pool, int &$cursor, int $count, string $dir, string $prefix): array
    {
        return collect(range(1, $count))->map(function (int $i) use (&$cursor, $pool, $dir, $prefix): ?string {
            $url = $pool[$cursor % count($pool)];
            $cursor++;

            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $path = "{$dir}/{$prefix}-{$i}.jpg";
            Storage::disk('public')->put($path, $response->body());

            return Storage::disk('public')->url($path);
        })->filter()->values()->all();
    }
}
