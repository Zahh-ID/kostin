<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('area_m2')->nullable();
            $table->enum('bathroom_type', ['inside', 'outside'])->nullable();
            $table->unsignedInteger('base_price');
            $table->unsignedInteger('deposit')->default(0);
            $table->json('facilities_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
