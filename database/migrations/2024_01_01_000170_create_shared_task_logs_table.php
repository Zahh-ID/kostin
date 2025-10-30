<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_task_id')->constrained('shared_tasks')->cascadeOnDelete();
            $table->timestamp('run_at');
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('photo_url')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_task_logs');
    }
};
