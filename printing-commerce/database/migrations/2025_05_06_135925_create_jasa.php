<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jasa', function (Blueprint $table) {
            $table->id('id_jasa');
            $table->uuid();
            $table->enum('kategori', ['logo', 'banner', 'poster']);
            $table->string('deskripsi_jasa', 500);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa');
    }
};