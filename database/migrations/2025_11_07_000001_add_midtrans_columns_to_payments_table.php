<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            if (! Schema::hasColumn('payments', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')
                    ->nullable()
                    ->after('submitted_by');
                $table->index('midtrans_order_id');
            }

            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')
                    ->nullable()
                    ->after('status');
            }

            if (! Schema::hasColumn('payments', 'raw_webhook_json')) {
                $table->json('raw_webhook_json')
                    ->nullable()
                    ->after('rejection_reason');
            }
        });

        if (Schema::hasColumn('payments', 'order_id')) {
            DB::statement('UPDATE payments SET midtrans_order_id = order_id WHERE midtrans_order_id IS NULL AND order_id IS NOT NULL');
        }

        if (Schema::hasColumn('payments', 'settlement_time')) {
            DB::statement('UPDATE payments SET paid_at = settlement_time WHERE paid_at IS NULL AND settlement_time IS NOT NULL');
        }

        if (Schema::hasColumn('payments', 'midtrans_response')) {
            DB::statement('UPDATE payments SET raw_webhook_json = midtrans_response WHERE raw_webhook_json IS NULL AND midtrans_response IS NOT NULL');
        }

        if (Schema::hasColumn('payments', 'user_id')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE payments MODIFY user_id BIGINT UNSIGNED NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE payments ALTER COLUMN user_id DROP NOT NULL');
            }
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            if (Schema::hasColumn('payments', 'midtrans_order_id')) {
                $table->dropColumn(['midtrans_order_id']);
            }

            if (Schema::hasColumn('payments', 'paid_at')) {
                $table->dropColumn(['paid_at']);
            }

            if (Schema::hasColumn('payments', 'raw_webhook_json')) {
                $table->dropColumn(['raw_webhook_json']);
            }
        });
    }
};
