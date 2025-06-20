<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editor', function (Blueprint $table) {
            $table->id('id_editor');
            $table->string('uuid')->unique();
            $table->string('nama_editor', 50);
            $table->enum('jenis_kelamin',['laki-laki','perempuan'])->nullable();
            $table->string('no_telpon', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editor');
    }
};