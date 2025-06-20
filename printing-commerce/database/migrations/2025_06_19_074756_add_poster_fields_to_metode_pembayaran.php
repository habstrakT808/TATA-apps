<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the columns
        Schema::table('metode_pembayaran', function (Blueprint $table) {
            // Add new poster fields
            $table->string('bahan_poster')->default('Art Paper');
            $table->string('ukuran_poster')->default('A3');
            $table->string('total_harga_poster')->default('150.000');
        });
        
        // Then update the existing records
        DB::table('metode_pembayaran')->update([
            'bahan_poster' => 'Art Paper',
            'ukuran_poster' => 'A3',
            'total_harga_poster' => '150.000'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metode_pembayaran', function (Blueprint $table) {
            // Remove the poster fields
            $table->dropColumn(['bahan_poster', 'ukuran_poster', 'total_harga_poster']);
        });
    }
};
