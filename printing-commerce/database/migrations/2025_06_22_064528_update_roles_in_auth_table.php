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
        // Perbarui role untuk akun yang sudah ada berdasarkan email
        DB::table('auth')
            ->where('email', 'SuperAdmin@gmail.com')
            ->update(['role' => 'super_admin']);
            
        DB::table('auth')
            ->where('email', 'adminchat@gmail.com')
            ->update(['role' => 'admin_chat']);
            
        DB::table('auth')
            ->where('email', 'editor@gmail.com')
            ->update(['role' => 'admin_pesanan']);
        
        // Perbarui role admin lainnya menjadi super_admin
        DB::table('auth')
            ->where('role', 'admin')
            ->whereNotIn('email', ['SuperAdmin@gmail.com', 'adminchat@gmail.com', 'editor@gmail.com'])
            ->update(['role' => 'super_admin']);
        
        // Add two new admin users if they don't exist already
        if (!DB::table('auth')->where('role', 'admin_chat')->exists()) {
            // Create admin_chat user
            $authId = DB::table('auth')->insertGetId([
                'email' => 'admin_chat@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin_chat'
            ]);
            
            // Create admin record
            DB::table('admin')->insert([
                'id_auth' => $authId,
                'nama_admin' => 'Admin Chat',
                'uuid' => \Illuminate\Support\Str::uuid()->toString()
            ]);
        }
        
        if (!DB::table('auth')->where('role', 'admin_pesanan')->exists()) {
            // Create admin_pesanan user
            $authId = DB::table('auth')->insertGetId([
                'email' => 'admin_pesanan@example.com',
                'password' => bcrypt('password123'),
                'role' => 'admin_pesanan'
            ]);
            
            // Create admin record
            DB::table('admin')->insert([
                'id_auth' => $authId,
                'nama_admin' => 'Admin Pesanan',
                'uuid' => \Illuminate\Support\Str::uuid()->toString()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke role sebelumnya
        DB::table('auth')
            ->whereIn('role', ['super_admin', 'admin_chat', 'admin_pesanan'])
            ->update(['role' => 'admin']);
        
        // Delete admin_chat and admin_pesanan users
        $chatAuth = DB::table('auth')->where('role', 'admin_chat')->first();
        if ($chatAuth) {
            DB::table('admin')->where('id_auth', $chatAuth->id_auth)->delete();
            DB::table('auth')->where('id_auth', $chatAuth->id_auth)->delete();
        }
        
        $pesananAuth = DB::table('auth')->where('role', 'admin_pesanan')->first();
        if ($pesananAuth) {
            DB::table('admin')->where('id_auth', $pesananAuth->id_auth)->delete();
            DB::table('auth')->where('id_auth', $pesananAuth->id_auth)->delete();
        }
    }
};
