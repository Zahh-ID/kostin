<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Models\User;
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
        $admin = User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $owner = User::factory()->owner()->create([
            'name' => 'Property Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $tenant = User::factory()->tenant()->create([
            'name' => 'Sample Tenant',
            'email' => 'tenant@example.com',
            'password' => Hash::make('password'),
        ]);

        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'Kost Harmoni',
            'status' => 'approved',
            'photos' => [
                'https://via.placeholder.com/800x600.png?text=Kost+Photo+1',
            ],
        ]);

        $roomType = RoomType::factory()->create([
            'property_id' => $property->id,
            'name' => 'Standard',
            'base_price' => 1500000,
            'deposit' => 500000,
            'facilities_json' => [
                'wifi' => true,
                'ac' => true,
                'laundry' => false,
            ],
        ]);

        $room101 = Room::factory()->create([
            'room_type_id' => $roomType->id,
            'room_code' => '101',
            'status' => 'available',
            'custom_price' => null,
        ]);

        Room::factory()->create([
            'room_type_id' => $roomType->id,
            'room_code' => '102',
            'status' => 'available',
            'custom_price' => null,
        ]);

        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room101->id,
            'start_date' => Carbon::now()->startOfMonth(),
            'price_per_month' => 1500000,
            'billing_day' => 1,
            'deposit_amount' => 500000,
            'grace_days' => 3,
            'late_fee_per_day' => 25000,
            'status' => 'active',
        ]);

        $invoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'period_month' => Carbon::now()->month,
            'period_year' => Carbon::now()->year,
            'due_date' => Carbon::now()->startOfMonth()->day($contract->billing_day),
            'amount' => $contract->price_per_month,
            'late_fee' => 0,
            'total' => $contract->price_per_month,
            'status' => 'unpaid',
        ]);

        $task = SharedTask::factory()->create([
            'property_id' => $property->id,
            'title' => 'Kebersihan Koridor',
            'assignee_user_id' => $owner->id,
            'next_run_at' => Carbon::now()->addDay(),
        ]);

        SharedTaskLog::factory()->create([
            'shared_task_id' => $task->id,
            'completed_by' => $tenant->id,
        ]);

        AuditLog::factory()->create([
            'user_id' => $admin->id,
            'action' => 'seed',
            'entity' => 'system',
            'entity_id' => 1,
            'meta_json' => ['message' => 'Initial demo data created.'],
            'created_at' => Carbon::now(),
        ]);
    }
}
