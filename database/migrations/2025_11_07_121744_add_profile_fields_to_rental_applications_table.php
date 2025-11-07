<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('rental_applications', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('tenant_id');
            }

            if (! Schema::hasColumn('rental_applications', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('contact_phone');
            }

            if (! Schema::hasColumn('rental_applications', 'occupants_count')) {
                $table->unsignedTinyInteger('occupants_count')->default(1)->after('duration_months');
            }

            if (! Schema::hasColumn('rental_applications', 'budget_per_month')) {
                $table->unsignedBigInteger('budget_per_month')->nullable()->after('occupants_count');
            }

            if (! Schema::hasColumn('rental_applications', 'employment_status')) {
                $table->string('employment_status')->nullable()->after('budget_per_month');
            }

            if (! Schema::hasColumn('rental_applications', 'company_name')) {
                $table->string('company_name')->nullable()->after('employment_status');
            }

            if (! Schema::hasColumn('rental_applications', 'job_title')) {
                $table->string('job_title')->nullable()->after('company_name');
            }

            if (! Schema::hasColumn('rental_applications', 'monthly_income')) {
                $table->unsignedBigInteger('monthly_income')->nullable()->after('job_title');
            }

            if (! Schema::hasColumn('rental_applications', 'has_vehicle')) {
                $table->boolean('has_vehicle')->default(false)->after('monthly_income');
            }

            if (! Schema::hasColumn('rental_applications', 'vehicle_notes')) {
                $table->string('vehicle_notes')->nullable()->after('has_vehicle');
            }

            if (! Schema::hasColumn('rental_applications', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('vehicle_notes');
            }

            if (! Schema::hasColumn('rental_applications', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_applications', function (Blueprint $table): void {
            $columns = [
                'contact_phone',
                'contact_email',
                'occupants_count',
                'budget_per_month',
                'employment_status',
                'company_name',
                'job_title',
                'monthly_income',
                'has_vehicle',
                'vehicle_notes',
                'emergency_contact_name',
                'emergency_contact_phone',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('rental_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
