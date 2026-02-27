<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make transfer_ref unique (nullable, so NULLs are allowed but non-null values must be unique)
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['transfer_ref']);
            $table->unique('transfer_ref');
        });

        // Add check constraints (MySQL 8.0.16+ supports CHECK constraints)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transactions_amount_positive CHECK (amount > 0)');
            DB::statement('ALTER TABLE wallets ADD CONSTRAINT chk_wallets_opening_balance_non_negative CHECK (opening_balance >= 0)');
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['transfer_ref']);
            $table->index('transfer_ref');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions DROP CHECK chk_transactions_amount_positive');
            DB::statement('ALTER TABLE wallets DROP CHECK chk_wallets_opening_balance_non_negative');
        }
    }
};
