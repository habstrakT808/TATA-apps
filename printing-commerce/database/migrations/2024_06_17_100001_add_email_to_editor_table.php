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
        // Add the email column as nullable first
        Schema::table('editor', function (Blueprint $table) {
            $table->string('email', 45)->nullable()->after('nama_editor');
        });
        
        // Then update existing records with default emails
        DB::statement("UPDATE editor SET email = CONCAT('editor', id_editor, '@tata.com')");
        
        // Finally make the column required
        Schema::table('editor', function (Blueprint $table) {
            $table->string('email', 45)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('editor', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}; 