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
use App\Models\Chat;
use App\Models\Auth as AuthModel;

echo "=== Testing Database Structure ===\n\n";

// 1. Check chat_messages table structure
echo "Chat Messages Table Structure:\n";
$columns = DB::select('SHOW COLUMNS FROM chat_messages');
print_r($columns);

echo "\n\n=== Testing Chat Data ===\n\n";

// 2. Find a chat with messages
$chat = Chat::whereHas('messages')->first();

if (!$chat) {
    echo "No chats with messages found!\n";
    exit(1);
}

echo "Found chat: UUID={$chat->uuid}\n";
echo "User ID: " . ($chat->user_id ?? 'NULL') . "\n";
echo "Admin ID: " . ($chat->admin_id ?? 'NULL') . "\n\n";

// 3. Get the user and admin
$user = $chat->user_id ? User::find($chat->user_id) : null;
$admin = $chat->admin_id ? Admin::find($chat->admin_id) : null;

if ($user) {
    echo "User: ID={$user->id_user}, Auth ID={$user->id_auth}, Name={$user->nama_user}\n";
} else {
    echo "User not found!\n";
}

if ($admin) {
    echo "Admin: ID={$admin->id_admin}, Auth ID={$admin->id_auth}, Name={$admin->nama_admin}\n";
} else {
    echo "Admin not found!\n";
}
echo "\n";

// 4. Get messages for this chat
echo "Messages in this chat:\n";
$messages = ChatMessage::where('chat_uuid', $chat->uuid)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($messages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  UUID: {$message->uuid}\n";
    echo "  Sender ID: {$message->sender_id}\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Message: {$message->message}\n";
    
    // Test the getSenderAttribute method
    $sender = $message->sender;
    echo "  Sender Info: " . json_encode($sender) . "\n\n";
}

// 5. Check if admin messages are using id_auth
$adminMessages = ChatMessage::where('chat_uuid', $chat->uuid)
    ->where('sender_type', 'admin')
    ->get();

echo "\nAdmin Messages:\n";
foreach ($adminMessages as $message) {
    echo "Message: {$message->message}\n";
    echo "Sender ID: {$message->sender_id}\n";
    
    // Check if this sender_id matches admin's id_auth
    $adminBySenderId = Admin::where('id_auth', $message->sender_id)->first();
    if ($adminBySenderId) {
        echo "✅ Found admin by id_auth: {$adminBySenderId->nama_admin}\n";
    } else {
        echo "❌ No admin found with id_auth = {$message->sender_id}\n";
        
        // Try finding by id_admin
        $adminById = Admin::where('id_admin', $message->sender_id)->first();
        if ($adminById) {
            echo "Found admin by id_admin: {$adminById->nama_admin}\n";
        }
    }
    echo "\n";
}

echo "\n=== Testing Complete ===\n"; 