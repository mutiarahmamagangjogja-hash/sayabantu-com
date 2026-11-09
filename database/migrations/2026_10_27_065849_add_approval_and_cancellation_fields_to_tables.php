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
        Schema::table('mitras', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status_label');
            $table->boolean('can_reapply')->default(false)->after('rejection_reason');
            $table->integer('penalty_score')->default(0)->after('can_reapply');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable()->after('status');
            $table->string('cancelled_by')->nullable()->after('cancellation_reason');
            $table->string('escrow_status')->default('none')->after('cancelled_by');
            $table->unsignedBigInteger('refund_amount')->default(0)->after('escrow_status');
            $table->unsignedBigInteger('compensation_amount')->default(0)->after('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'can_reapply', 'penalty_score']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'cancelled_by', 'escrow_status', 'refund_amount', 'compensation_amount']);
        });
    }
};
