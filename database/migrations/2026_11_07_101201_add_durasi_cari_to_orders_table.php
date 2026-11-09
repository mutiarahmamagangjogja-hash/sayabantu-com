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
            $table->string('durasi_cari')->nullable()->default('60 Menit')->after('jadwal');
            $table->unsignedInteger('durasi_menit')->default(60)->after('durasi_cari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['durasi_cari', 'durasi_menit']);
        });
    }
};
