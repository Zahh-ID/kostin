<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding realistic data with images...');

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('seed/properties');
        Storage::disk('public')->makeDirectory('seed/rooms');

        // Image pools (Unsplash)
        $exteriorPool = [
            'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600596542815-2495db98dada?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600573472592-401b489a3cdc?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600573472550-8090b5e0745e?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600047509358-9dc75507daeb?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600566752355-35792bedcfe1?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600585152220-90363fe7e115?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1600573472968-02924e8a9571?auto=format&fit=crop&w=1400&q=80',
        ];

        $roomPool = [
            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1598928636135-d146006ff4be?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1552058544-f2b08422138a?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1560185007-c5ca9d2c014d?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1560184897-ae75f418493e?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505692069463-7e3409e70f0b?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505692794403-34d4982c9803?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1505691723518-36a5ac3be353?auto=format&fit=crop&w=1400&q=80',
        ];

        $exteriorCursor = 0;
        $roomCursor = 0;
        $faker = Faker::create('id_ID'); // Use Indonesian locale for realistic data

        // 1. Create Users
        $this->command?->info('Creating users...');

        $demoOwner = User::factory()->owner()->create([
            'name' => 'Owner Demo',
            'email' => 'owner@kostin.com',
            'password' => bcrypt('password'),
            'phone' => '081234567890',
        ]);

        $demoTenant = User::factory()->tenant()->create([
            'name' => 'Tenant Demo',
            'email' => 'tenant@kostin.com',
            'password' => bcrypt('password'),
            'phone' => '081234567891',
        ]);

        $owners = User::factory()->owner()->count(12)->create()->each(function ($u) use ($faker) {
            $u->update([
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
            ]);
        });

        $owners->push($demoOwner);

        $tenants = User::factory()->tenant()->count(40)->create()->each(function ($u) use ($faker) {
            $u->update([
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
            ]);
        });

        $tenants->push($demoTenant);

        // 2. Create Demo Owner Property (IMMEDIATE DATA)
        $this->command?->info('Creating demo owner property...');

        $demoProperty = Property::factory()->create([
            'owner_id' => $demoOwner->id,
            'name' => 'Kost Demo Premium',
            'address' => 'Jl. Demo Raya No. 1, Jakarta Selatan',
            'lat' => -6.200000,
            'lng' => 106.816666,
            'rules_text' => 'Dilarang merokok di dalam kamar. Tamu menginap maksimal 2 hari.',
            'status' => 'approved',
            'photos' => [],
        ]);

        $demoPhotos = $this->samplePhotos($exteriorPool, $exteriorCursor, 3, 'seed/properties', "real-property-{$demoProperty->id}");
        $demoProperty->update(['photos' => $demoPhotos]);

        // Create Rooms for Demo Property
        $demoRoomTypes = RoomType::factory()->count(2)->create([
            'property_id' => $demoProperty->id,
            'name' => fn() => $faker->randomElement(['VIP', 'Suite']) . ' ' . $faker->randomElement(['A', 'B']),
            'base_price' => 2500000,
        ]);

        foreach ($demoRoomTypes as $rt) {
            Collection::times(3, function (int $idx) use ($rt, $roomPool, &$roomCursor, $faker, $tenants): void {
                $room = Room::factory()->create([
                    'room_type_id' => $rt->id,
                    'room_code' => strtoupper(substr($rt->name, 0, 1)) . '-' . ($idx + 100),
                    'status' => 'available',
                    'photos_json' => [],
                ]);

                $roomPhotos = $this->samplePhotos($roomPool, $roomCursor, 2, 'seed/rooms', "real-room-{$room->id}");
                $room->update(['photos_json' => $roomPhotos]);

                // Create Contract & Payments
                if ($faker->boolean(80)) {
                    // Create a NEW tenant for demo property to avoid conflict/termination
                    $tenant = User::factory()->tenant()->create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'phone' => $faker->phoneNumber,
                    ]);

                    $startDate = now()->subMonths(rand(3, 12));

                    $contract = Contract::factory()->create([
                        'room_id' => $room->id,
                        'tenant_id' => $tenant->id,
                        'start_date' => $startDate,
                        'end_date' => (clone $startDate)->addYear(),
                        'price_per_month' => $room->price ?? $room->roomType->base_price,
                        'status' => 'active',
                    ]);

                    $room->update(['status' => 'occupied']);

                    $monthsActive = $startDate->diffInMonths(now());
                    for ($i = 0; $i <= $monthsActive; $i++) {
                        $invoiceDate = (clone $startDate)->addMonths($i);
                        if ($invoiceDate->isFuture())
                            continue;

                        $invoice = Invoice::factory()->create([
                            'contract_id' => $contract->id,
                            'amount' => $contract->price_per_month,
                            'status' => 'paid',
                            'due_date' => $invoiceDate,
                            'created_at' => $invoiceDate,
                        ]);

                        Payment::factory()->create([
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->amount,
                            'payment_type' => 'bank_transfer',
                            'status' => 'success',
                            'created_at' => $invoiceDate->addDays(rand(0, 5)),
                        ]);
                    }
                }
            });
        }

        // 2. Create Properties
        $this->command?->info('Creating properties...');

        $propertyCount = 30;
        $ownersCount = $owners->count();

        Collection::times($propertyCount, function (int $i) use ($owners, $ownersCount, $exteriorPool, &$exteriorCursor, $roomPool, &$roomCursor, $faker, $tenants): void {
            $owner = $owners[($i - 1) % $ownersCount];

            // Generate realistic property name
            $area = $faker->city;
            $type = $faker->randomElement(['Kost', 'Wisma', 'Residence', 'House', 'Living']);
            $suffix = $faker->randomElement(['Putra', 'Putri', 'Campur', 'Syariah', 'Exclusive', 'Elite', 'Nyaman']);
            $name = "$type $suffix $area " . $faker->firstName;

            $property = Property::factory()->create([
                'owner_id' => $owner->id,
                'name' => $name,
                'address' => $faker->address,
                'lat' => $faker->latitude(-8, -6), // Java area roughly
                'lng' => $faker->longitude(106, 114),
                'rules_text' => $faker->paragraph,
                'status' => 'approved',
                'photos' => [],
            ]);

            // Download and save property photos
            $photos = $this->samplePhotos($exteriorPool, $exteriorCursor, 3, 'seed/properties', "real-property-{$property->id}");
            $property->update(['photos' => $photos]);

            // 3. Create Rooms (Min 4 per property)
            $roomTypeCount = rand(2, 3);
            $roomsCreated = 0;

            $roomTypes = RoomType::factory()->count($roomTypeCount)->create([
                'property_id' => $property->id,
                'name' => fn() => $faker->randomElement(['Standard', 'Deluxe', 'VIP', 'Suite', 'Economy']) . ' ' . $faker->randomElement(['A', 'B', 'C']),
                'base_price' => $faker->numberBetween(800000, 3000000),
            ]);

            foreach ($roomTypes as $rt) {
                // Ensure we get at least 4 rooms total per property
                $roomsPerType = max(2, ceil((4 - $roomsCreated) / ($roomTypeCount - $roomsCreated > 0 ? $roomTypeCount - $roomsCreated : 1)));
                // Simple logic: just make 2-4 rooms per type to be safe and exceed 4 total
                $roomsPerType = rand(2, 4);

                Collection::times($roomsPerType, function (int $idx) use ($rt, $property, $roomPool, &$roomCursor, &$roomsCreated): void {
                    $room = Room::factory()->create([
                        'room_type_id' => $rt->id,
                        'room_code' => strtoupper(substr($rt->name, 0, 1)) . '-' . ($idx + 100),
                        'status' => 'available',
                        'photos_json' => [],
                    ]);

                    $roomPhotos = $this->samplePhotos($roomPool, $roomCursor, 2, 'seed/rooms', "real-room-{$room->id}");
                    $room->update(['photos_json' => $roomPhotos]);
                    $roomsCreated++;
                });
            }

            // 4. Create Contracts & Payments (for some rooms)
            $property->rooms->each(function ($room) use ($faker, $tenants) {
                if ($faker->boolean(70)) { // 70% chance of being occupied
                    $tenant = $tenants->random();
                    $startDate = now()->subMonths(rand(1, 12));

                    $contract = Contract::factory()->create([
                        'room_id' => $room->id,
                        'tenant_id' => $tenant->id,
                        'start_date' => $startDate,
                        'end_date' => (clone $startDate)->addYear(),
                        'price' => $room->price ?? $room->roomType->base_price,
                        'status' => 'active',
                    ]);

                    $room->update(['status' => 'occupied']);

                    // Create invoices and payments for past months
                    $monthsActive = $startDate->diffInMonths(now());
                    for ($i = 0; $i <= $monthsActive; $i++) {
                        $invoiceDate = (clone $startDate)->addMonths($i);
                        if ($invoiceDate->isFuture())
                            continue;

                        $invoice = Invoice::factory()->create([
                            'contract_id' => $contract->id,
                            'amount' => $contract->price,
                            'status' => 'paid',
                            'due_date' => $invoiceDate,
                            'created_at' => $invoiceDate,
                        ]);

                        Payment::factory()->create([
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->amount,
                            'payment_type' => 'bank_transfer',
                            'status' => 'success',
                            'created_at' => $invoiceDate->addDays(rand(0, 5)),
                        ]);
                    }
                }
            });

            $this->command?->info("Property {$property->id} created with {$roomsCreated} rooms.");
        });

        $this->command?->info('Real data seeding completed!');
    }

    /**
     * Download photos from URL and save to storage
     */
    private function samplePhotos(array $pool, int &$cursor, int $count, string $dir, string $prefix): array
    {
        return collect(range(1, $count))->map(function (int $i) use (&$cursor, $pool, $dir, $prefix): ?string {
            $url = $pool[$cursor % count($pool)];
            $cursor++;

            try {
                $response = Http::timeout(10)->get($url);

                if (!$response->successful()) {
                    return null;
                }

                $path = "{$dir}/{$prefix}-{$i}.jpg";
                Storage::disk('public')->put($path, $response->body());

                return Storage::disk('public')->url($path);
            } catch (\Exception $e) {
                return null;
            }
        })->filter()->values()->all();
    }
}
