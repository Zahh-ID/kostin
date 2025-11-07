<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Midtrans IDs
            $table->string('order_id')->unique();
            $table->string('transaction_id')->nullable();
            
            // Payment Info
            $table->string('payment_type'); // qris, bank_transfer, gopay, etc
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, success, failed, cancelled, expired
            $table->string('transaction_status')->nullable(); // from Midtrans
            
            // QRIS specific
            $table->text('qris_string')->nullable();
            
            // Bank Transfer specific
            $table->json('va_numbers')->nullable();
            
            // Response data
            $table->json('midtrans_response')->nullable();
            
            // Timestamps
            $table->timestamp('settlement_time')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('status');
            $table->index(['invoice_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};