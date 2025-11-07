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
        Schema::table('properties', function (Blueprint $table) {
            $table->text('moderation_notes')->nullable()->after('status');
            $table->foreignId('moderated_by')->nullable()->after('moderation_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('moderated_at');
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn('moderation_notes');
        });
    }
};
