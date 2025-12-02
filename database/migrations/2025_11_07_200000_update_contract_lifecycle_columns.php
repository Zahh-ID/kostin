<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->timestamp('submitted_at')->nullable()->after('status');
            $table->timestamp('activated_at')->nullable()->after('submitted_at');
            $table->timestamp('terminated_at')->nullable()->after('activated_at');
            $table->text('termination_reason')->nullable()->after('terminated_at');
        });

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('contracts', function (Blueprint $table): void {
                $table->string('status_new', 32)->default('active')->after('status');
            });

            DB::table('contracts')->update(['status_new' => DB::raw('status')]);

            DB::table('contracts')
                ->where('status_new', 'ended')
                ->update(['status_new' => 'terminated']);

            Schema::table('contracts', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('contracts', function (Blueprint $table): void {
                $table->renameColumn('status_new', 'status');
            });
        } else {
            // Expand status enum and map legacy values for MySQL.
            DB::statement("
                ALTER TABLE contracts
                MODIFY status ENUM('draft','submitted','active','pending_renewal','terminated','canceled','expired')
                NOT NULL DEFAULT 'active'
            ");
        }

        // Map legacy statuses to the new set.
        DB::table('contracts')
            ->where('status', 'ended')
            ->update([
                'status' => 'terminated',
                'terminated_at' => DB::raw('COALESCE(terminated_at, end_date)'),
            ]);

        DB::table('contracts')
            ->where('status', 'active')
            ->update([
                'submitted_at' => DB::raw('COALESCE(submitted_at, created_at)'),
                'activated_at' => DB::raw('COALESCE(activated_at, start_date)'),
            ]);
    }

    public function down(): void
    {
        // Map statuses back where possible.
        DB::table('contracts')
            ->where('status', 'terminated')
            ->update(['status' => 'ended']);

        if (DB::getDriverName() === 'sqlite') {
            Schema::table('contracts', function (Blueprint $table): void {
                $table->string('status_old', 32)->default('active')->after('status');
            });

            DB::table('contracts')->update(['status_old' => DB::raw('status')]);

            Schema::table('contracts', function (Blueprint $table): void {
                $table->dropColumn('status');
            });

            Schema::table('contracts', function (Blueprint $table): void {
                $table->renameColumn('status_old', 'status');
            });
        } else {
            DB::table('contracts')
                ->whereIn('status', ['draft', 'submitted', 'pending_renewal', 'expired'])
                ->update(['status' => 'active']);

            DB::statement("
                ALTER TABLE contracts
                MODIFY status ENUM('active','ended','canceled')
                NOT NULL DEFAULT 'active'
            ");
        }

        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropColumn(['submitted_at', 'activated_at', 'terminated_at', 'termination_reason']);
        });
    }
};
