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

echo "=== Chat Messages for UUID 65642798-3e79-42ea-89a1-d124ab0d1994 ===\n\n";

// Get all messages for this chat
$messages = DB::select('
    SELECT cm.*, 
           a.id_admin, a.nama_admin, a.id_auth as admin_id_auth,
           u.id_user, u.nama_user, u.id_auth as user_id_auth
    FROM chat_messages cm 
    LEFT JOIN admin a ON cm.sender_type = "admin" AND cm.sender_id = a.id_auth
    LEFT JOIN users u ON cm.sender_type = "user" AND cm.sender_id = u.id_auth
    WHERE cm.chat_uuid = "65642798-3e79-42ea-89a1-d124ab0d1994" 
    ORDER BY cm.created_at DESC
    LIMIT 10;
');

// Display messages
foreach ($messages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  ID: {$message->id}\n";
    echo "  UUID: {$message->uuid}\n";
    echo "  Sender ID: {$message->sender_id}\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Message: {$message->message}\n";
    
    if ($message->sender_type === 'admin') {
        echo "  Admin Info:\n";
        echo "    ID Admin: " . ($message->id_admin ?? 'NULL') . "\n";
        echo "    Name: " . ($message->nama_admin ?? 'NULL') . "\n";
        echo "    ID Auth: " . ($message->admin_id_auth ?? 'NULL') . "\n";
    } else {
        echo "  User Info:\n";
        echo "    ID User: " . ($message->id_user ?? 'NULL') . "\n";
        echo "    Name: " . ($message->nama_user ?? 'NULL') . "\n";
        echo "    ID Auth: " . ($message->user_id_auth ?? 'NULL') . "\n";
    }
    echo "\n";
}

echo "\n=== Testing with Eloquent Models ===\n\n";

// Get messages using Eloquent
$eloquentMessages = ChatMessage::where('chat_uuid', '65642798-3e79-42ea-89a1-d124ab0d1994')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($eloquentMessages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  ID: {$message->id}\n";
    echo "  Sender ID: {$message->sender_id}\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Message: {$message->message}\n";
    
    // Get sender using relationship
    if ($message->sender_type === 'admin') {
        $admin = $message->admin;
        echo "  Admin from relationship: " . ($admin ? $admin->nama_admin : 'NULL') . "\n";
        
        // Try direct query
        $adminDirect = Admin::where('id_auth', $message->sender_id)->first();
        echo "  Admin from direct query: " . ($adminDirect ? $adminDirect->nama_admin : 'NULL') . "\n";
    }
    
    // Try getSenderAttribute
    $sender = $message->sender;
    echo "  Sender from attribute: " . json_encode($sender) . "\n";
    echo "\n";
}

echo "\n=== Testing Complete ===\n"; 