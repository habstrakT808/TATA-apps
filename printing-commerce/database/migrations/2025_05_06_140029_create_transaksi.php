<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('order_id')->unique();
            $table->unsignedInteger('jumlah');
            $table->enum('status_transaksi', ['belum_bayar', 'menunggu_konfirmasi', 'lunas', 'dibatalkan', 'expired']);
            $table->string('bukti_pembayaran')->nullable();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->text('catatan_transaksi')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->dateTime('expired_at');
            $table->timestamps();
            $table->unsignedBigInteger('id_metode_pembayaran');
            $table->foreign('id_metode_pembayaran')->references('id_metode_pembayaran')->on('metode_pembayaran')->onDelete('cascade');
            $table->unsignedBigInteger('id_pesanan');
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};