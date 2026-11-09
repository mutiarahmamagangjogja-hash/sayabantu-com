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
            $table->string('nego_by')->nullable()->after('metode'); // 'customer' or 'mitra'
            $table->string('agreed_by')->nullable()->after('nego_by'); // 'customer' or 'mitra'
            $table->string('agreed_price')->nullable()->after('agreed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['nego_by', 'agreed_by', 'agreed_price']);
        });
    }
};
