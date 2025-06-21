<?php
// database/migrations/2025_06_21_000000_create_metode_pembayaran_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metode_pembayaran', function (Blueprint $table) {
            $table->id('id_metode_pembayaran');
            $table->uuid('uuid')->unique();
            $table->string('nama_metode', 100);
            $table->enum('jenis_metode', ['bank_transfer', 'e_wallet', 'virtual_account']);
            $table->string('no_metode_pembayaran', 50);
            $table->string('deskripsi_1', 200)->nullable(); // Atas nama
            $table->string('deskripsi_2', 200)->nullable(); // Keterangan tambahan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pembayaran');
    }
}; 