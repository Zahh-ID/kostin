<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmptyPropertySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Owner
        $owner = User::firstOrCreate(
            ['email' => 'empty_owner@example.com'],
            [
                'name' => 'Juragan Kos Kosong',
                'password' => Hash::make('password'),
                'phone' => '081299998888',
                'role' => 'owner',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Property
        $property = Property::create([
            'owner_id' => $owner->id,
            'name' => 'Kost Kosong Melompong',
            'address' => 'Jl. Sunyi Senyap No. 0, Jakarta Selatan',
            'lat' => -6.200000,
            'lng' => 106.816666,
            'rules_text' => "Dilarang berisik.\nJaga kebersihan.",
            'photos' => [
                'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1400&q=80',
                'https://images.unsplash.com/photo-1513584685908-2274653fa36f?auto=format&fit=crop&w=1400&q=80'
            ],
            'status' => 'approved',
            'moderated_at' => now(),
            'moderation_notes' => 'Seeder generated property.',
        ]);

        // 3. Create Room Types
        $typeA = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Tipe A (Kamar Mandi Dalam)',
            'area_m2' => 12,
            'bathroom_type' => 'inside',
            'base_price' => 1500000,
            'deposit' => 500000,
            'facilities_json' => ['wifi' => true, 'ac' => true, 'bed' => true, 'cupboard' => true],
        ]);

        $typeB = RoomType::create([
            'property_id' => $property->id,
            'name' => 'Tipe B (Kamar Mandi Luar)',
            'area_m2' => 9,
            'bathroom_type' => 'outside',
            'base_price' => 1000000,
            'deposit' => 300000,
            'facilities_json' => ['wifi' => true, 'fan' => true, 'bed' => true],
        ]);

        // 4. Create Empty Rooms
        // Type A Rooms
        for ($i = 1; $i <= 5; $i++) {
            Room::create([
                'room_type_id' => $typeA->id,
                'room_code' => 'A0' . $i,
                'status' => 'available',
                'custom_price' => null,
                'description' => 'Kamar Tipe A nomor ' . $i . ' siap huni.',
                'photos_json' => ['https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1400&q=80'],
            ]);
        }

        // Type B Rooms
        for ($i = 1; $i <= 5; $i++) {
            Room::create([
                'room_type_id' => $typeB->id,
                'room_code' => 'B0' . $i,
                'status' => 'available',
                'custom_price' => null,
                'description' => 'Kamar Tipe B nomor ' . $i . ' siap huni.',
                'photos_json' => ['https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1400&q=80'],
            ]);
        }

        $this->command->info('Seeder Property Kosong berhasil dijalankan!');
        $this->command->info('Owner: empty_owner@example.com / password');
    }
}
