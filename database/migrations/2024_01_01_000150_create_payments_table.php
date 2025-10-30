<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('midtrans_order_id')->nullable();
            $table->enum('payment_type', ['qris'])->default('qris');
            $table->unsignedInteger('amount');
            $table->enum('status', ['success', 'pending', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_webhook_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
