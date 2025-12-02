<?php

declare(strict_types=1);

use App\Models\Contract;
use App\Models\User;

it('allows tenant to download own contract as pdf', function (): void {
    $tenant = User::factory()->tenant()->create();
    $contract = Contract::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->actingAs($tenant)
        ->get('/api/v1/tenant/contracts/'.$contract->id.'/pdf');

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
    $response->assertDownload("kontrak-{$contract->id}.pdf");
});

it('forbids downloading someone else contract', function (): void {
    $tenant = User::factory()->tenant()->create();
    $otherContract = Contract::factory()->create();

    $response = $this->actingAs($tenant)
        ->get('/api/v1/tenant/contracts/'.$otherContract->id.'/pdf');

    $response->assertForbidden();
});
