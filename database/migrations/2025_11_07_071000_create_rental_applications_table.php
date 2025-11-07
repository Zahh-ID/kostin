<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->date('preferred_start_date')->nullable();
            $table->unsignedInteger('duration_months')->default(12);
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('tenant_notes')->nullable();
            $table->text('owner_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('terms_text')->nullable();
            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_applications');
    }
};
