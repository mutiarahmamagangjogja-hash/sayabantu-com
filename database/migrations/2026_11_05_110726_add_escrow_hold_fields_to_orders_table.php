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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('escrow_hold_reason')->nullable()->after('escrow_status');
            $table->timestamp('escrow_released_at')->nullable()->after('escrow_hold_reason');
            $table->unsignedBigInteger('admin_fee')->default(0)->after('escrow_released_at');
            $table->unsignedBigInteger('mitra_fee')->default(0)->after('admin_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['escrow_hold_reason', 'escrow_released_at', 'admin_fee', 'mitra_fee']);
        });
    }
};
