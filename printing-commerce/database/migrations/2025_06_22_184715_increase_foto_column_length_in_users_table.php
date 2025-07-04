<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Increase the length of the foto column in the users table to accommodate longer photo URLs.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('foto')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto', 50)->nullable()->change();
        });
    }
};
