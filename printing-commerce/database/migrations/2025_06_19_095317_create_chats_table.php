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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id_user')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')->references('id_admin')->on('admin')->nullOnDelete();
            $table->string('pesanan_uuid')->nullable();
            $table->text('last_message')->nullable();
            $table->integer('unread_count')->default(0);
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('user_id');
            $table->index('admin_id');
            $table->index('pesanan_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
