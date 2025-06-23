<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jasa', function (Blueprint $table) {
            $table->index('kategori');
            $table->index(['id_jasa', 'kategori']);
        });

        Schema::table('paket_jasa', function (Blueprint $table) {
            $table->index('id_jasa');
            $table->index(['id_jasa', 'harga_paket_jasa']);
            $table->index('kelas_jasa');
        });
    }

    public function down()
    {
        Schema::table('jasa', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropIndex(['id_jasa', 'kategori']);
        });

        Schema::table('paket_jasa', function (Blueprint $table) {
            $table->dropIndex(['id_jasa']);
            $table->dropIndex(['id_jasa', 'harga_paket_jasa']);
            $table->dropIndex(['kelas_jasa']);
        });
    }
}; 