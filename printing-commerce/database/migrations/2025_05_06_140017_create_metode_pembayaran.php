<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metode_pembayaran', function (Blueprint $table) {
            $table->id('id_metode_pembayaran');
            $table->uuid('uuid');
            $table->string('nama_metode_pembayaran', 12);
            $table->string('no_metode_pembayaran', 20);
            $table->string('deskripsi_1', 500);
            $table->string('deskripsi_2', 500);
            $table->string('thumbnail', 50);
            $table->string('icon', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pembayaran');
    }
};