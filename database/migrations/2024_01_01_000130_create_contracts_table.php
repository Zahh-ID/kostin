<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('price_per_month');
            $table->unsignedTinyInteger('billing_day')->default(1);
            $table->unsignedInteger('deposit_amount')->default(0);
            $table->unsignedTinyInteger('grace_days')->default(3);
            $table->unsignedInteger('late_fee_per_day')->default(0);
            $table->enum('status', ['active', 'ended', 'canceled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
