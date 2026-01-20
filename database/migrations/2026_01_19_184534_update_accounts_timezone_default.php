<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing accounts to use Asia/Baghdad timezone
        DB::table('accounts')->update(['timezone' => 'Asia/Baghdad']);
        
        // Update default in schema
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('timezone')->default('Asia/Baghdad')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('timezone')->default('UTC')->change();
        });
    }
};
