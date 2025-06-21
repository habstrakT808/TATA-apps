<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

echo "=== RAW OUTPUT FOR CHAT MESSAGES ===\n\n";

// 1. Get raw data from database for admin messages
echo "=== Raw Database Output for Admin Messages ===\n\n";

$adminMessages = DB::select('
    SELECT cm.*, 
           a.id_admin, a.nama_admin, a.id_auth as admin_auth_id
    FROM chat_messages cm
    JOIN admin a ON cm.sender_id = a.id_auth
    WHERE cm.sender_type = "admin"
    ORDER BY cm.created_at DESC
    LIMIT 5;
');

echo "Admin messages found: " . count($adminMessages) . "\n\n";
foreach ($adminMessages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  ID: {$message->id}\n";
    echo "  Chat UUID: {$message->chat_uuid}\n";
    echo "  Sender ID: {$message->sender_id} (Admin Auth ID: {$message->admin_auth_id})\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Admin Name: {$message->nama_admin} (ID: {$message->id_admin})\n";
    echo "  Message: {$message->message}\n\n";
}

// 2. Get raw data from database for specific chat
echo "=== Raw Database Output for Specific Chat ===\n\n";

// Get a chat that has admin messages
$chat = Chat::whereHas('messages', function($query) {
    $query->where('sender_type', 'admin');
})->first();

if (!$chat) {
    echo "No chat with admin messages found!\n";
    exit(1);
}

echo "Found chat: UUID={$chat->uuid}\n";
echo "User ID: " . ($chat->user_id ?? 'NULL') . "\n";
echo "Admin ID: " . ($chat->admin_id ?? 'NULL') . "\n\n";

$messages = DB::select("
    SELECT cm.*, 
           CASE 
               WHEN cm.sender_type = 'admin' THEN a.nama_admin
               WHEN cm.sender_type = 'user' THEN u.nama_user
               ELSE 'Unknown'
           END as sender_name,
           CASE 
               WHEN cm.sender_type = 'admin' THEN a.id_admin
               WHEN cm.sender_type = 'user' THEN u.id_user
               ELSE NULL
           END as sender_real_id,
           CASE 
               WHEN cm.sender_type = 'admin' THEN a.id_auth
               WHEN cm.sender_type = 'user' THEN u.id_auth
               ELSE NULL
           END as sender_auth_id
    FROM chat_messages cm
    LEFT JOIN admin a ON cm.sender_type = 'admin' AND cm.sender_id = a.id_auth
    LEFT JOIN users u ON cm.sender_type = 'user' AND cm.sender_id = u.id_auth
    WHERE cm.chat_uuid = ?
    ORDER BY cm.created_at ASC
", [$chat->uuid]);

echo "Messages found: " . count($messages) . "\n\n";
foreach ($messages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  ID: {$message->id}\n";
    echo "  Sender ID: {$message->sender_id}\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Sender Name: {$message->sender_name}\n";
    echo "  Sender Real ID: {$message->sender_real_id}\n";
    echo "  Sender Auth ID: {$message->sender_auth_id}\n";
    echo "  Message: {$message->message}\n\n";
}

// 3. Test the API endpoint
echo "=== Raw API Response ===\n\n";

// Find the user associated with this chat
$user = null;
if ($chat->user_id) {
    $user = User::find($chat->user_id);
    echo "Found user associated with chat: {$user->nama_user}\n";
} 

if (!$user) {
    $user = User::first();
    echo "Using first user since chat user not found.\n";
}

echo "User: {$user->nama_user} (ID: {$user->id_user}, Auth ID: {$user->id_auth})\n\n";

// Create a token for this user
$plainTextToken = bin2hex(random_bytes(32));
$hashedToken = hash('sha256', $plainTextToken);

DB::table('personal_access_tokens')->insert([
    'tokenable_type' => 'App\\Models\\Auth',
    'tokenable_id' => $user->id_auth,
    'name' => 'raw_output_test',
    'token' => $hashedToken,
    'abilities' => '["*"]',
    'created_at' => now(),
    'updated_at' => now()
]);

$tokenId = DB::getPdo()->lastInsertId();
$fullToken = $tokenId . '|' . $plainTextToken;

// Set up authentication
\Illuminate\Support\Facades\Auth::shouldReceive('id')
    ->andReturn($user->id_auth);

// Create controller and call method
$controller = new \App\Http\Controllers\Mobile\ChatController();

// Call the getMessages method
$request = new \Illuminate\Http\Request();
$request->merge(['chat_uuid' => $chat->uuid]);
$response = $controller->getMessages($request);

// Output response
echo "API Response from getMessages:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
echo "\n\n";

// Call the getMessagesByPesanan method if pesanan_uuid is available
if ($chat->pesanan_uuid) {
    $response = $controller->getMessagesByPesanan($chat->pesanan_uuid);
    echo "API Response from getMessagesByPesanan:\n";
    echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
    echo "\n\n";
}

// Clean up
DB::table('personal_access_tokens')->where('id', $tokenId)->delete();

echo "=== End of Raw Output ===\n"; 