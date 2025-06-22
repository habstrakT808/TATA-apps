<?php

// Load Laravel's database connection
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Begin transaction
    DB::beginTransaction();
    
    // Disable foreign key checks to avoid constraint errors
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Get all user IDs for deletion references
    $userIds = DB::table('users')->pluck('id_user')->toArray();
    
    if (!empty($userIds)) {
        // Delete records from dependent tables related to users
        echo "Deleting data from related tables...\n";
        
        // Get pesanan IDs belonging to these users
        $pesananIds = DB::table('pesanan')->whereIn('id_user', $userIds)->pluck('id_pesanan')->toArray();
        
        if (!empty($pesananIds)) {
            // Delete from statistik_pesanan
            DB::table('statistik_pesanan')->whereIn('id_pesanan', $pesananIds)->delete();
            echo "- Deleted statistik_pesanan records\n";
            
            // Delete from review
            DB::table('review')->whereIn('id_pesanan', $pesananIds)->delete();
            echo "- Deleted review records\n";
            
            // Check if these tables exist before attempting to delete
            $tables = ['revisi', 'catatan_pesanan'];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->whereIn('id_pesanan', $pesananIds)->delete();
                    echo "- Deleted {$table} records\n";
                }
            }
            
            // Delete from pesanan
            DB::table('pesanan')->whereIn('id_pesanan', $pesananIds)->delete();
            echo "- Deleted pesanan records\n";
        }
        
        // Check if verifikasi_user table exists
        if (Schema::hasTable('verifikasi_user')) {
            DB::table('verifikasi_user')->whereIn('id_user', $userIds)->delete();
            echo "- Deleted verifikasi_user records\n";
        }
        
        // Delete users
        echo "Deleting all users...\n";
        DB::table('users')->truncate();
        
        // Get auth IDs for user role
        $userAuthIds = DB::table('auth')->where('role', 'user')->pluck('id_auth')->toArray();
        if (!empty($userAuthIds)) {
            // Delete auth records for users
            DB::table('auth')->whereIn('id_auth', $userAuthIds)->delete();
            echo "- Deleted user authentication records\n";
        }
    } else {
        echo "No users found in the database.\n";
    }
    
    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    // Commit transaction
    DB::commit();
    
    echo "All users have been deleted successfully.\n";
} catch (Exception $e) {
    // Rollback in case of error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
} 