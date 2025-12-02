<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            $table->timestamp('expires_at')->nullable()->after('due_date');
            $table->text('status_reason')->nullable()->after('status');
            $table->unsignedBigInteger('primary_payment_id')->nullable()->after('status');
        });

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table): void {
                $table->string('status_new', 32)->default('unpaid')->after('status_reason');
            });

            DB::table('invoices')->update(['status_new' => DB::raw('status')]);

            Schema::table('invoices', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('invoices', function (Blueprint $table): void {
                $table->renameColumn('status_new', 'status');
            });
        } else {
            DB::statement("
                ALTER TABLE invoices
                MODIFY status ENUM('unpaid','paid','overdue','canceled','pending_verification','expired')
                NOT NULL DEFAULT 'unpaid'
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table): void {
                $table->string('status_old', 32)->default('unpaid');
            });

            DB::table('invoices')->update(['status_old' => DB::raw('status')]);

            Schema::table('invoices', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('invoices', function (Blueprint $table): void {
                $table->renameColumn('status_old', 'status');
            });
        } else {
            DB::statement("
                ALTER TABLE invoices
                MODIFY status ENUM('unpaid','paid','overdue','canceled','pending_verification')
                NOT NULL DEFAULT 'unpaid'
            ");
        }

        Schema::table('invoices', function (Blueprint $table): void {
            $table->dropColumn(['expires_at', 'status_reason', 'primary_payment_id']);
        });
    }
};
