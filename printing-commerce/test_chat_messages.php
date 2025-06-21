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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

echo "=== Testing Chat Messages ===\n\n";

// 1. Find a chat with messages from the specific UUID we know has admin messages
$chatUuid = '65642798-3e79-42ea-89a1-d124ab0d1994';
$chat = Chat::where('uuid', $chatUuid)->first();

if (!$chat) {
    echo "Chat with UUID $chatUuid not found!\n";
    exit(1);
}

echo "Found chat: UUID={$chat->uuid}\n";
echo "User ID: " . ($chat->user_id ?? 'NULL') . "\n";
echo "Admin ID: " . ($chat->admin_id ?? 'NULL') . "\n\n";

// 2. Get messages for this chat
echo "Messages in this chat:\n";
$messages = ChatMessage::where('chat_uuid', $chat->uuid)
    ->orderBy('created_at', 'desc')
    ->get();

echo "Found " . $messages->count() . " messages\n\n";

foreach ($messages as $index => $message) {
    echo "Message #{$index}:\n";
    echo "  ID: {$message->id}\n";
    echo "  UUID: {$message->uuid}\n";
    echo "  Sender ID: {$message->sender_id}\n";
    echo "  Sender Type: {$message->sender_type}\n";
    echo "  Message: {$message->message}\n";
    
    // Test the getSenderAttribute method
    $sender = $message->sender;
    echo "  Sender Info: " . json_encode($sender) . "\n\n";
}

// 3. Test the API endpoint directly
echo "=== Testing API Endpoint ===\n\n";

// Find the user associated with this chat
$user = null;
if ($chat->user_id) {
    $user = User::find($chat->user_id);
    echo "Found user associated with chat: {$user->nama_user}\n";
} 

// If no user found, get any user
if (!$user) {
    $user = User::first();
    if (!$user) {
        echo "No users found in the database!\n";
        exit(1);
    }
    echo "Using first user since chat user not found.\n";
}

echo "User: {$user->nama_user} (ID: {$user->id_user}, Auth ID: {$user->id_auth})\n\n";

// Find the auth record for this user
$auth = DB::table('auth')->where('id_auth', $user->id_auth)->first();
if (!$auth) {
    echo "Auth record not found for this user! Creating a mock auth record.\n";
    
    // Create a mock auth record
    $authId = DB::table('auth')->insertGetId([
        'email' => 'test_' . time() . '@example.com',
        'password' => bcrypt('password'),
        'role' => 'user',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    // Update user's id_auth
    DB::table('users')->where('id_user', $user->id_user)->update(['id_auth' => $authId]);
    
    // Refresh user
    $user = User::find($user->id_user);
    $auth = DB::table('auth')->where('id_auth', $user->id_auth)->first();
    
    echo "Created mock auth record with ID: {$auth->id_auth}\n";
}

// Create a token for this user
$plainTextToken = bin2hex(random_bytes(32));
$hashedToken = hash('sha256', $plainTextToken);

DB::table('personal_access_tokens')->insert([
    'tokenable_type' => 'App\\Models\\Auth',
    'tokenable_id' => $user->id_auth,
    'name' => 'test_chat_token',
    'token' => $hashedToken,
    'abilities' => '["*"]',
    'created_at' => now(),
    'updated_at' => now()
]);

$tokenId = DB::getPdo()->lastInsertId();
$fullToken = $tokenId . '|' . $plainTextToken;

echo "Created token: $fullToken\n\n";

// Set up authentication
$auth = (object)$auth;
$auth->id_auth = $user->id_auth;

// Mock the Auth facade
\Illuminate\Support\Facades\Auth::shouldReceive('id')
    ->andReturn($user->id_auth);

// Create the request
$request = new Request();
$request->merge(['chat_uuid' => $chat->uuid]);

// Create controller and call method
$controller = new \App\Http\Controllers\Mobile\ChatController();
$response = $controller->getMessages($request);

// Output response
echo "API Response:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
echo "\n\n";

// Clean up
DB::table('personal_access_tokens')->where('id', $tokenId)->delete();

echo "=== Testing Complete ===\n"; 