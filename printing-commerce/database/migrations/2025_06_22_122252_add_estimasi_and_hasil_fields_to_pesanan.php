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
        Schema::table('pesanan', function (Blueprint $table) {
            // Tambahkan field untuk estimasi pengerjaan yang lebih detail
            $table->date('estimasi_mulai')->nullable()->after('estimasi_waktu');
            $table->date('estimasi_selesai')->nullable()->after('estimasi_mulai');
            
            // Tambahkan field untuk hasil desain
            $table->string('file_hasil_desain')->nullable()->after('estimasi_selesai');
            
            // Tambahkan field untuk status pengerjaan yang lebih detail
            $table->enum('status_pengerjaan', ['menunggu', 'diproses', 'dikerjakan', 'selesai'])
                  ->default('menunggu')
                  ->after('status_pesanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('estimasi_mulai');
            $table->dropColumn('estimasi_selesai');
            $table->dropColumn('file_hasil_desain');
            $table->dropColumn('status_pengerjaan');
        });
    }
};
