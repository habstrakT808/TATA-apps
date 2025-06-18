<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->uuid();
            $table->string('deskripsi');
            $table->enum('status_pesanan', ['pending', 'diproses', 'menunggu_editor', 'dikerjakan', 'revisi', 'menunggu_review', 'selesai', 'dibatalkan']);
            $table->unsignedInteger('total_harga');
            $table->dateTime('estimasi_waktu');
            $table->unsignedTinyInteger('maksimal_revisi');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_jasa');
            $table->foreign('id_jasa')->references('id_jasa')->on('jasa')->onDelete('cascade');
            $table->unsignedBigInteger('id_paket_jasa');
            $table->foreign('id_paket_jasa')->references('id_paket_jasa')->on('paket_jasa')->onDelete('cascade');
            $table->unsignedBigInteger('id_editor')->nullable();
            $table->foreign('id_editor')->references('id_editor')->on('editor')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};