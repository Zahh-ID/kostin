<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('rental_applications', 'terms_text')) {
                $table->text('terms_text')->nullable()->after('rejected_at');
            }

            if (! Schema::hasColumn('rental_applications', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('terms_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_applications', function (Blueprint $table): void {
            if (Schema::hasColumn('rental_applications', 'terms_text')) {
                $table->dropColumn('terms_text');
            }

            if (Schema::hasColumn('rental_applications', 'terms_accepted_at')) {
                $table->dropColumn('terms_accepted_at');
            }
        });
    }
};
