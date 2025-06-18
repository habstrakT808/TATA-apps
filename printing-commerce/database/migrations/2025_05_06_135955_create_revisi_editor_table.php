<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisi_editor', function (Blueprint $table) {
            $table->id('id_revisi_editor');
            $table->string('nama_file');
            $table->text('catatan_editor')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('id_editor');
            $table->foreign('id_editor')->references('id_editor')->on('editor')->onDelete('cascade');
            $table->unsignedBigInteger('id_revisi')->nullable();
            $table->foreign('id_revisi')->references('id_revisi')->on('revisi')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisi_editor');
    }
}; 