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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('layanan');
            $table->string('jadwal');
            $table->text('alamat');
            $table->text('note')->nullable();
            $table->string('biaya');
            $table->unsignedBigInteger('nominal')->default(0);
            $table->string('metode')->default('Nego');
            $table->string('voucher_code')->nullable();
            $table->string('voucher_discount')->nullable();
            $table->string('gender')->default('Bebas');
            $table->string('status')->default('menunggu_mitra');
            $table->unsignedTinyInteger('step')->default(4);
            $table->string('mitra_id')->nullable();
            $table->string('mitra_name')->nullable();
            $table->string('mitra_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
