<?php

namespace App\Providers;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SharedTask;
use App\Models\SharedTaskLog;
use App\Policies\ContractPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\RoomPolicy;
use App\Policies\RoomTypePolicy;
use App\Policies\SharedTaskLogPolicy;
use App\Policies\SharedTaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Property::class => PropertyPolicy::class,
        RoomType::class => RoomTypePolicy::class,
        Room::class => RoomPolicy::class,
        Contract::class => ContractPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Payment::class => PaymentPolicy::class,
        SharedTask::class => SharedTaskPolicy::class,
        SharedTaskLog::class => SharedTaskLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
