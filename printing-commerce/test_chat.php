<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Admin;

echo "=== Testing Chat Message Model ===\n\n";

// 1. Get a recent message
$message = ChatMessage::where('sender_type', 'admin')
    ->orderBy('created_at', 'desc')
    ->first();

if (!$message) {
    echo "No admin messages found!\n";
    exit(1);
}

echo "Found message: " . $message->message . "\n";
echo "Sender ID: " . $message->sender_id . "\n";
echo "Sender Type: " . $message->sender_type . "\n\n";

// 2. Test the getSenderAttribute method
$senderInfo = $message->sender;
echo "Sender Info from getSenderAttribute():\n";
print_r($senderInfo);
echo "\n";

// 3. Test the admin relationship
$admin = $message->admin;
echo "Admin from relationship:\n";
if ($admin) {
    echo "Admin ID: " . $admin->id_admin . "\n";
    echo "Admin Name: " . $admin->nama_admin . "\n";
} else {
    echo "Admin relationship returned null!\n";
    
    // Debug by querying directly
    $adminDirect = Admin::where('id_auth', $message->sender_id)->first();
    if ($adminDirect) {
        echo "But direct query found admin:\n";
        echo "Admin ID: " . $adminDirect->id_admin . "\n";
        echo "Admin Name: " . $adminDirect->nama_admin . "\n";
    } else {
        echo "Direct query also found no admin with id_auth = " . $message->sender_id . "\n";
    }
}

echo "\n=== Testing Complete ===\n"; 