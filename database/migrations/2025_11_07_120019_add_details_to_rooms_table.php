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
        Schema::table('rooms', function (Blueprint $table): void {
            if (! Schema::hasColumn('rooms', 'description')) {
                $table->text('description')->after('status');
            }

            if (! Schema::hasColumn('rooms', 'photos_json')) {
                $table->json('photos_json')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table): void {
            if (Schema::hasColumn('rooms', 'photos_json')) {
                $table->dropColumn('photos_json');
            }

            if (Schema::hasColumn('rooms', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
