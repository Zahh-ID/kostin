<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('room_code');
            $table->unsignedInteger('custom_price')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->json('facilities_override_json')->nullable();
            $table->timestamps();

            $table->unique(['room_type_id', 'room_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
