<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisi_user', function (Blueprint $table) {
            $table->id('id_revisi_user');
            $table->string('nama_file');
            $table->text('catatan_user')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('id_revisi')->nullable();
            $table->foreign('id_revisi')->references('id_revisi')->on('revisi')->onDelete('cascade');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisi_user');
    }
}; 