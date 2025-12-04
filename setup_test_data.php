<?php

use App\Models\User;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Property;
use App\Models\RoomType;

$user = User::where('email', 'zahhkai@gmail.com')->first();

if (!$user) {
    echo "User not found!\n";
    exit(1);
}

// Ensure we have a property, room type, and room
$property = Property::first();
if (!$property) {
    $property = Property::factory()->create(['owner_id' => $user->id]); // Just assign to user for simplicity if no owner
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

$room = Room::where('room_type_id', $roomType->id)->first();
if (!$room) {
    $room = Room::create([
        'room_type_id' => $roomType->id,
        'name' => '101',
        'status' => 'available'
    ]);
}

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
    'amount' => 500000,
    'total' => 500000,
    'status' => 'unpaid',
    'status_reason' => 'Test Invoice for Reminder',
    'external_order_id' => 'TEST-' . time(),
]);

echo "Created Invoice #{$invoice->id} due on " . $invoice->due_date->format('Y-m-d') . "\n";
