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
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->string('mitra_id')->nullable()->unique();
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('phone')->index();
            $table->string('email')->nullable();
            $table->string('kota')->default('Jakarta');
            $table->string('area')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pin');
            $table->string('keahlian')->nullable();
            $table->string('service')->nullable();
            $table->string('jaminan')->nullable();
            $table->text('avatar')->nullable();
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->integer('tasks')->default(0);
            $table->string('status')->default('verifikasi');
            $table->string('status_label')->default('Menunggu Verifikasi & ACC Admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
