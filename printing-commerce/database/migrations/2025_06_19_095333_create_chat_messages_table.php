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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('chat_uuid');
            $table->string('sender_id');
            $table->enum('sender_type', ['user', 'admin']);
            $table->text('message')->nullable();
            $table->enum('message_type', ['text', 'image', 'file'])->default('text');
            $table->string('file_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('chat_uuid');
            $table->index('sender_id');
            $table->index('sender_type');
            $table->index('is_read');
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
