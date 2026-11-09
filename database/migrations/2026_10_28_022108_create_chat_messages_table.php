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
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('order_code')->index();
            $table->string('sender_type'); // 'customer' | 'mitra' | 'admin' | 'system'
            $table->string('sender_name');
            $table->string('sender_avatar')->nullable();
            $table->text('message');
            $table->string('message_type')->default('text'); // 'text', 'nego', 'deal_accepted', 'system'
            $table->json('metadata')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
