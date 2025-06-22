<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateEditorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, check if the editor role exists in the auth table
        $hasEditorRole = DB::select("SHOW COLUMNS FROM auth WHERE Field = 'role' AND Type LIKE '%editor%'");
        
        if (empty($hasEditorRole)) {
            // Add editor role to the enum
            DB::statement("ALTER TABLE auth MODIFY COLUMN role ENUM('super_admin', 'admin_chat', 'admin_pemesanan', 'user', 'editor') NOT NULL");
            $this->command->info('Editor role added to auth table');
        } else {
            $this->command->info('Editor role already exists in auth table');
        }
    }
}
