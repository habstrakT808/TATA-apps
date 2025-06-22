<?php

// Load Laravel's database connection
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check admin table
$adminCount = DB::table('admin')->count();
echo "Number of admin accounts: " . $adminCount . "\n";

// List admin accounts
echo "\nAdmin accounts:\n";
$admins = DB::table('admin')
    ->join('auth', 'admin.id_auth', '=', 'auth.id_auth')
    ->select('admin.nama_admin', 'auth.email', 'auth.role')
    ->get();

foreach ($admins as $admin) {
    echo "- {$admin->nama_admin} ({$admin->email}) - {$admin->role}\n";
}

// Check editor table
$editorCount = DB::table('editor')->count();
echo "\nNumber of editor accounts: " . $editorCount . "\n";

// List editor accounts
echo "\nEditor accounts:\n";
$editors = DB::table('editor')
    ->select('nama_editor', 'email')
    ->get();

foreach ($editors as $editor) {
    echo "- {$editor->nama_editor} ({$editor->email})\n";
} 