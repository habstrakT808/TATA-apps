<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Auth;
use App\Models\Admin;
use App\Models\Editor;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanupUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:users {--force : Force deletion without confirmation} {--keep-editors : Keep editor records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all users except for the specified email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $protectedEmail = 'jhodywiraputra@gmail.com';
        
        $this->info('Starting user cleanup process...');
        
        // Get the auth ID of the protected user
        $protectedAuth = Auth::where('email', $protectedEmail)->first();
        
        if (!$protectedAuth) {
            $this->error("Protected user with email {$protectedEmail} not found!");
            return 1;
        }
        
        $protectedAuthId = $protectedAuth->id_auth;
        $this->info("Protected user found with auth ID: {$protectedAuthId}");
        
        // Count users to be deleted
        $userCount = User::where('id_auth', '!=', $protectedAuthId)->count();
        $adminCount = Admin::where('id_auth', '!=', $protectedAuthId)->count();
        $authCount = Auth::where('id_auth', '!=', $protectedAuthId)
                        ->where('email', '!=', $protectedEmail)
                        ->count();
        $editorCount = Editor::count();
        
        $this->info("Found {$userCount} users, {$adminCount} admins, {$authCount} auth records, and {$editorCount} editors.");
        
        // Ask for confirmation unless --force is used
        if (!$this->option('force') && !$this->confirm('Do you want to proceed with deletion?')) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Delete all users except the protected one
            $deletedUserCount = User::where('id_auth', '!=', $protectedAuthId)->delete();
            $this->info("Deleted {$deletedUserCount} users");
            
            // Delete all admins except those related to the protected user
            $deletedAdminCount = Admin::where('id_auth', '!=', $protectedAuthId)->delete();
            $this->info("Deleted {$deletedAdminCount} admins");
            
            // Delete all auth records except the protected one
            $deletedAuthCount = Auth::where('id_auth', '!=', $protectedAuthId)
                                   ->where('email', '!=', $protectedEmail)
                                   ->delete();
            $this->info("Deleted {$deletedAuthCount} auth records");
            
            // Delete all editors if not keeping them
            if (!$this->option('keep-editors')) {
                // First, update any foreign key references to null
                $this->info("Updating foreign key references to editors...");
                
                // Check if pesanan table has id_editor column
                if (Schema::hasColumn('pesanan', 'id_editor')) {
                    Pesanan::query()->update(['id_editor' => null]);
                    $this->info("Updated pesanan.id_editor references to null");
                }
                
                // Check for any other tables that might reference editor
                $tables = DB::select("
                    SELECT TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_NAME = 'editor'
                    AND REFERENCED_COLUMN_NAME = 'id_editor'
                    AND TABLE_SCHEMA = DATABASE()
                ");
                
                foreach ($tables as $table) {
                    $tableName = $table->TABLE_NAME;
                    $columnName = $table->COLUMN_NAME;
                    
                    if ($tableName != 'pesanan' || $columnName != 'id_editor') {
                        $this->info("Updating {$tableName}.{$columnName} references to null");
                        DB::table($tableName)->update([$columnName => null]);
                    }
                }
                
                // Now delete all editors
                $deletedEditorCount = Editor::count();
                Editor::query()->delete();
                $this->info("Deleted {$deletedEditorCount} editors");
            } else {
                $this->info("Skipping editor deletion as requested");
            }
            
            DB::commit();
            $this->info('User cleanup completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during cleanup: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 