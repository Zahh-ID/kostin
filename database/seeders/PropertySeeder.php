<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'owner@example.com')->first();

        if (! $owner) {
            $owner = User::factory()->create([
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'password' => bcrypt('password'),
                'role' => 'owner',
            ]);
        }

        Property::create([
            'owner_id' => $owner->id,
            'name' => 'Kos Melati',
            'address' => 'Jl. Melati No. 1, Jakarta',
            'lat' => -6.2088,
            'lng' => 106.8456,
            'rules_text' => 'Dilarang merokok di dalam kamar.',
            'photos' => [
                'https://images.unsplash.com/photo-1580587771525-78b9dba3b914',
                'https://images.unsplash.com/photo-1570129477492-45c003edd2be',
                'https://images.unsplash.com/photo-1568605114967-8130f3a36994',
            ],
            'status' => 'approved',
        ]);

        Property::create([
            'owner_id' => $owner->id,
            'name' => 'Kos Mawar',
            'address' => 'Jl. Mawar No. 2, Bandung',
            'lat' => -6.9175,
            'lng' => 107.6191,
            'rules_text' => 'Dilarang membawa hewan peliharaan.',
            'photos' => [
                'https://images.unsplash.com/photo-1512917774080-9991f1c4c750',
                'https://images.unsplash.com/photo-1600585154340-be6161a56a0c',
                'https://images.unsplash.com/photo-1572120360610-d971b9d7767c',
            ],
            'status' => 'approved',
        ]);

        Property::create([
            'owner_id' => $owner->id,
            'name' => 'Kos Anggrek',
            'address' => 'Jl. Anggrek No. 3, Surabaya',
            'lat' => -7.2575,
            'lng' => 112.7521,
            'rules_text' => 'Jam malam pukul 22:00.',
            'photos' => [
                'https://images.unsplash.com/photo-1598228723793-52759bba239c',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688',
                'https://images.unsplash.com/photo-1554995207-c18c203602cb',
            ],
            'status' => 'approved',
        ]);
    }
}
