<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            if (! Schema::hasColumn('invoices', 'months_count')) {
                $table->unsignedTinyInteger('months_count')
                    ->default(1)
                    ->after('period_year');
            }

            if (! Schema::hasColumn('invoices', 'coverage_start_month')) {
                $table->unsignedTinyInteger('coverage_start_month')
                    ->nullable()
                    ->after('months_count');
            }

            if (! Schema::hasColumn('invoices', 'coverage_start_year')) {
                $table->unsignedSmallInteger('coverage_start_year')
                    ->nullable()
                    ->after('coverage_start_month');
            }

            if (! Schema::hasColumn('invoices', 'coverage_end_month')) {
                $table->unsignedTinyInteger('coverage_end_month')
                    ->nullable()
                    ->after('coverage_start_year');
            }

            if (! Schema::hasColumn('invoices', 'coverage_end_year')) {
                $table->unsignedSmallInteger('coverage_end_year')
                    ->nullable()
                    ->after('coverage_end_month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            foreach (['coverage_end_year', 'coverage_end_month', 'coverage_start_year', 'coverage_start_month', 'months_count'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
