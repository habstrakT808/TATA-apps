<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Editor;

echo "====== Auth Records ======\n";
$auths = Auth::all();
if ($auths->count() > 0) {
    foreach ($auths as $auth) {
        echo "ID: {$auth->id_auth}, Email: {$auth->email}, Role: {$auth->role}\n";
    }
} else {
    echo "No auth records found.\n";
}

echo "\n====== User Records ======\n";
$users = User::all();
if ($users->count() > 0) {
    foreach ($users as $user) {
        echo "ID: {$user->id_user}, Name: {$user->nama_user}, Auth ID: {$user->id_auth}, Phone: {$user->no_telpon}\n";
    }
} else {
    echo "No user records found.\n";
}

echo "\n====== Admin Records ======\n";
$admins = Admin::all();
if ($admins->count() > 0) {
    foreach ($admins as $admin) {
        echo "ID: {$admin->id_admin}, Name: {$admin->nama_admin}, Auth ID: {$admin->id_auth}, Phone: {$admin->no_telpon}\n";
    }
} else {
    echo "No admin records found.\n";
}

echo "\n====== Editor Records ======\n";
$editors = Editor::all();
if ($editors->count() > 0) {
    foreach ($editors as $editor) {
        echo "ID: {$editor->id_editor}, Name: {$editor->nama_editor}, Email: {$editor->email}, Phone: {$editor->no_telpon}\n";
    }
} else {
    echo "No editor records found.\n";
} 