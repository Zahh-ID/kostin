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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('submitted_by')
                ->nullable()
                ->after('invoice_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('manual_method')
                ->nullable()
                ->after('payment_type');

            $table->string('proof_path')
                ->nullable()
                ->after('manual_method');

            $table->string('proof_filename')
                ->nullable()
                ->after('proof_path');

            $table->text('notes')
                ->nullable()
                ->after('proof_filename');

            $table->foreignId('verified_by')
                ->nullable()
                ->after('notes')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verified_by');

            $table->text('rejection_reason')
                ->nullable()
                ->after('verified_at');
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('payments', function (Blueprint $table): void {
                $table->string('payment_type')
                    ->default('qris')
                    ->change();
                $table->string('status')
                    ->default('pending')
                    ->change();
            });
        } else {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('qris','manual_bank_transfer','manual_cash') NOT NULL DEFAULT 'qris'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','waiting_verification','success','failed','rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['submitted_by']);
            $table->dropForeign(['verified_by']);

            $table->dropColumn([
                'submitted_by',
                'manual_method',
                'proof_path',
                'proof_filename',
                'notes',
                'verified_by',
                'verified_at',
                'rejection_reason',
            ]);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('payments', function (Blueprint $table): void {
                $table->string('payment_type')
                    ->default('qris')
                    ->change();
                $table->string('status')
                    ->default('pending')
                    ->change();
            });
        } else {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_type ENUM('qris') NOT NULL DEFAULT 'qris'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('success','pending','failed') NOT NULL DEFAULT 'pending'");
        }
    }
};
