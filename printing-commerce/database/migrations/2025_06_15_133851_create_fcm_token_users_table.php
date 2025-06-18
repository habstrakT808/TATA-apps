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
        Schema::create('fcm_token', function (Blueprint $table) {
            $table->id('id_fcm_token');
            $table->string('fcm_token')->nullable();
            $table->timestamp('fcm_token_updated_at')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_type')->nullable(); // android/ios
            $table->timestamps();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_token');
    }
};
