<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->unsignedSmallInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->date('due_date');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('late_fee')->default(0);
            $table->unsignedInteger('total');
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'canceled'])->default('unpaid');
            $table->string('external_order_id')->nullable();
            $table->json('qris_payload')->nullable();
            $table->timestamps();

            $table->unique(['contract_id', 'period_month', 'period_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
