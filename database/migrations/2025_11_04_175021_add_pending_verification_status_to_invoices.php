<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table): void {
                $table->string('status_new')->default('unpaid');
            });

            DB::table('invoices')->update([
                'status_new' => DB::raw("CASE WHEN status IN ('unpaid','paid','overdue','canceled') THEN status ELSE 'unpaid' END"),
            ]);

            Schema::table('invoices', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('invoices', function (Blueprint $table): void {
                $table->renameColumn('status_new', 'status');
            });
        } else {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid','paid','overdue','canceled','pending_verification') NOT NULL DEFAULT 'unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table): void {
                $table->string('status_old')->default('unpaid');
            });

            DB::table('invoices')->update([
                'status_old' => DB::raw("CASE WHEN status IN ('paid','overdue','canceled') THEN status ELSE 'unpaid' END"),
            ]);

            Schema::table('invoices', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('invoices', function (Blueprint $table): void {
                $table->renameColumn('status_old', 'status');
            });
        } else {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid','paid','overdue','canceled') NOT NULL DEFAULT 'unpaid'");
        }
    }
};
