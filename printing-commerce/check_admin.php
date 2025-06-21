<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Auth as AuthModel;

echo "=== Admin Table Structure ===\n\n";

// Check admin table structure
$columns = DB::select('SHOW COLUMNS FROM admin');
print_r($columns);

echo "\n\n=== Auth Table Structure ===\n\n";

// Check auth table structure
$columns = DB::select('SHOW COLUMNS FROM auth');
print_r($columns);

echo "\n\n=== Admin Data ===\n\n";

// Get admin with ID 65
$admin = Admin::find(65);
if ($admin) {
    echo "Admin ID 65 found:\n";
    echo "ID: {$admin->id_admin}\n";
    echo "Name: {$admin->nama_admin}\n";
    echo "ID Auth: {$admin->id_auth}\n";
    
    // Get auth record
    $auth = AuthModel::find($admin->id_auth);
    if ($auth) {
        echo "\nAuth record found:\n";
        echo "ID Auth: {$auth->id_auth}\n";
        echo "Email: {$auth->email}\n";
        echo "Role: {$auth->role}\n";
    } else {
        echo "\nNo auth record found for id_auth = {$admin->id_auth}\n";
    }
} else {
    echo "No admin found with ID 65\n";
}

// Find admin by id_auth
echo "\n\nSearching for admin with id_auth = 166:\n";
$adminByAuth = Admin::where('id_auth', 166)->first();
if ($adminByAuth) {
    echo "Found admin:\n";
    echo "ID: {$adminByAuth->id_admin}\n";
    echo "Name: {$adminByAuth->nama_admin}\n";
    echo "ID Auth: {$adminByAuth->id_auth}\n";
} else {
    echo "No admin found with id_auth = 166\n";
}

echo "\n=== Testing Complete ===\n"; 