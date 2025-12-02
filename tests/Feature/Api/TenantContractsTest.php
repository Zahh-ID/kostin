<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('lists only contracts for the authenticated tenant', function (): void {
    $tenant = User::factory()->tenant()->create();
    $otherTenant = User::factory()->tenant()->create();

    $ownContract = Contract::factory()->create(['tenant_id' => $tenant->id]);
    Contract::factory()->create(['tenant_id' => $otherTenant->id]);

    $response = $this->actingAs($tenant)
        ->getJson('/api/v1/tenant/contracts');

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->has('data', 1)
        ->where('data.0.id', $ownContract->id)
        ->has('links')
        ->has('meta')
    );
});

it('shows a single contract for the tenant', function (): void {
    $tenant = User::factory()->tenant()->create();
    $contract = Contract::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($tenant)
        ->getJson("/api/v1/tenant/contracts/{$contract->id}");

    $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
        ->where('data.id', $contract->id)
        ->where('data.tenant.id', $tenant->id)
    );
});
