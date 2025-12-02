<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('saved_searches');
    }

    public function down(): void
    {
        Schema::create('saved_searches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('filters');
            $table->boolean('notification_enabled')->default(false);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'notification_enabled']);
        });
    }
};
