<?php

use App\Models\User;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Support\Facades\Hash;

$email = 'nihdivaa@gmail.com';
$user = User::where('email', $email)->first();

if (!$user) {
    $user = User::create([
        'name' => 'Nihdivaa Test',
        'email' => $email,
        'password' => Hash::make('password'),
        'role' => 'tenant',
    ]);
    echo "Created User: $email\n";
}

// Ensure we have a property, room type, and room
$property = Property::first();
if (!$property) {
    $property = Property::factory()->create(['owner_id' => $user->id]);
}

$roomType = RoomType::where('property_id', $property->id)->first();
if (!$roomType) {
    $roomType = RoomType::create([
        'property_id' => $property->id,
        'name' => 'Standard Room',
        'price' => 500000,
        'total_rooms' => 10
    ]);
}

$room = Room::create([
    'room_type_id' => $roomType->id,
    'name' => 'TEST-' . rand(100, 999),
    'room_code' => 'TEST-' . rand(100, 999),
    'status' => 'available'
]);

// Create Contract
$contract = Contract::create([
    'tenant_id' => $user->id,
    'room_id' => $room->id,
    'start_date' => now()->subMonth(),
    'end_date' => now()->addYear(),
    'price_per_month' => 500000,
    'billing_day' => 1,
    'status' => 'active',
    'submitted_at' => now()->subMonth(),
    'activated_at' => now()->subMonth(),
]);

// Create Invoice
$invoice = Invoice::create([
    'contract_id' => $contract->id,
    'period_month' => now()->month,
    'period_year' => now()->year,
    'months_count' => 1,
    'due_date' => now()->addDays(3), // DUE IN 3 DAYS
    'amount' => 750000,
    'total' => 750000,
    'status' => 'unpaid',
    'status_reason' => 'Test Invoice for Nihdivaa',
    'external_order_id' => 'TEST-NIHDIVAA-' . time(),
]);

echo "Created Invoice #{$invoice->id} for {$email}\n";
