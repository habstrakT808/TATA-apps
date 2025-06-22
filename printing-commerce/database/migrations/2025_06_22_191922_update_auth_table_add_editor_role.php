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
        // For MySQL, we need to modify the column
        DB::statement("ALTER TABLE auth MODIFY COLUMN role ENUM('super_admin', 'admin_chat', 'admin_pemesanan', 'user', 'editor')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the change
        DB::statement("ALTER TABLE auth MODIFY COLUMN role ENUM('super_admin', 'admin_chat', 'admin_pemesanan', 'user')");
    }
};
