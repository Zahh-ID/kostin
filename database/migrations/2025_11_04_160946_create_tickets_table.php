<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('category', ['technical', 'payment', 'content', 'abuse']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_review', 'escalated', 'resolved', 'rejected'])->default('open');
            $table->nullableMorphs('related');
            $table->json('tags')->nullable();
            $table->unsignedInteger('sla_minutes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['reporter_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
