<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a superadmin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'SuperAdmin@gmail.com';
        $password = 'Admin@1234567890';
        
        $this->info('Creating superadmin account...');
        
        // Check if the superadmin already exists
        $existingAuth = Auth::where('email', $email)->first();
        
        if ($existingAuth) {
            $this->info("Superadmin account with email {$email} already exists!");
            return 0;
        }
        
        // Create auth record
        $auth = Auth::create([
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'superadmin'
        ]);
        
        $this->info("Created auth record with ID: {$auth->id_auth}");
        
        // Create admin record
        $admin = Admin::create([
            'uuid' => Str::uuid()->toString(),
            'nama_admin' => 'Super Admin',
            'id_auth' => $auth->id_auth
        ]);
        
        $this->info("Created admin record with ID: {$admin->id_admin}");
        $this->info("Superadmin account created successfully!");
        
        return 0;
    }
} 