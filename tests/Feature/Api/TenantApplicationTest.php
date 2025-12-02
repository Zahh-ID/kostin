<?php

declare(strict_types=1);

use App\Models\Property;
use App\Models\RentalApplication;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('creates rental application via api', function (): void {
    $tenant = User::factory()->tenant()->create();
    $property = Property::factory()->create(['status' => 'approved']);
    $roomType = $property->roomTypes()->create([
        'name' => 'Standard',
        'area_m2' => 12,
        'bathroom_type' => 'inside',
        'base_price' => 1500000,
        'deposit' => 500000,
        'facilities_json' => ['wifi' => true],
    ]);
    $room = $roomType->rooms()->create([
        'room_code' => 'A1',
        'status' => 'available',
        'custom_price' => 1600000,
        'description' => 'Kamar uji',
        'photos_json' => [],
    ]);

    $payload = [
        'property_id' => $property->id,
        'room_type_id' => $roomType->id,
        'room_id' => $room->id,
        'preferred_start_date' => now()->addDay()->toDateString(),
        'duration_months' => 12,
        'occupants_count' => 1,
        'budget_per_month' => 2000000,
        'employment_status' => 'Karyawan',
        'monthly_income' => 5000000,
        'contact_phone' => '08123456789',
        'emergency_contact_name' => 'Darurat',
        'emergency_contact_phone' => '0811111111',
        'terms_agreed' => true,
    ];

    $response = $this->actingAs($tenant)->postJson('/api/v1/tenant/applications', $payload);

    $response->assertCreated()->assertJsonPath('status', 'pending');

    expect(RentalApplication::where('tenant_id', $tenant->id)->count())->toBe(1);
});
