<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\User;
use App\Models\Admin;

// Find a user and their chat for testing
$chat = Chat::whereHas('messages')->first();
if (!$chat) {
    echo "No chats with messages found!\n";
    exit(1);
}

// Get the user for this chat
$user = User::find($chat->user_id);
if (!$user) {
    echo "User not found for this chat!\n";
    exit(1);
}

// Find the auth record for this user
$auth = DB::table('auth')->where('id_auth', $user->id_auth)->first();
if (!$auth) {
    echo "Auth record not found for this user!\n";
    exit(1);
}

echo "=== Testing Mobile API Response ===\n\n";
echo "User: {$user->nama_user} (ID: {$user->id_user}, Auth ID: {$user->id_auth})\n";
echo "Chat UUID: {$chat->uuid}\n";
echo "Pesanan UUID: " . ($chat->pesanan_uuid ?? 'NULL') . "\n\n";

// Create a test token for the user
$token = bin2hex(random_bytes(32));
DB::table('personal_access_tokens')->insert([
    'tokenable_type' => 'App\\Models\\Auth',
    'tokenable_id' => $user->id_auth,
    'name' => 'test_token',
    'token' => hash('sha256', $token),
    'abilities' => '["*"]',
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Created test token for user\n\n";

// Create a request to the mobile API
$request = Request::create('/api/mobile/chat/messages', 'GET', [
    'chat_uuid' => $chat->uuid
]);

// Set authorization header
$request->headers->set('Authorization', 'Bearer ' . $token);

// Dispatch the request
$response = app()->handle($request);

// Output response
echo "API Response Status: " . $response->getStatusCode() . "\n";
echo "API Response Content:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);

// Clean up the test token
DB::table('personal_access_tokens')
    ->where('tokenable_id', $user->id_auth)
    ->where('name', 'test_token')
    ->delete();

echo "\n\n=== Testing Complete ===\n"; 