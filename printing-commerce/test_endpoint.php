<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\User;
use App\Models\Admin;
use App\Models\Auth as AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

echo "=== Testing Chat Messages Endpoint ===\n\n";

// Find a chat with pesanan_uuid
$chat = Chat::whereNotNull('pesanan_uuid')->first();

if (!$chat) {
    echo "No chat with pesanan_uuid found!\n";
    exit(1);
}

echo "Using chat: UUID={$chat->uuid}, Pesanan UUID={$chat->pesanan_uuid}\n";
echo "User ID in chat: " . ($chat->user_id ?? 'NULL') . "\n";
echo "Admin ID in chat: " . ($chat->admin_id ?? 'NULL') . "\n\n";

// Find a user to use for testing
$user = null;

// First try to get the user from the chat
if ($chat->user_id) {
    $user = User::find($chat->user_id);
}

// If no user found, get any user
if (!$user) {
    $user = User::first();
    if (!$user) {
        echo "No users found in the database!\n";
        exit(1);
    }
    echo "Using different user since chat user not found.\n";
}

echo "User: {$user->nama_user} (ID: {$user->id_user}, Auth ID: {$user->id_auth})\n\n";

// Find the auth record for this user
$auth = DB::table('auth')->where('id_auth', $user->id_auth)->first();
if (!$auth) {
    echo "Auth record not found for this user!\n";
    exit(1);
}

// Create a test token for the user
$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);

// Insert the token into the database
DB::table('personal_access_tokens')->insert([
    'tokenable_type' => 'App\\Models\\Auth',
    'tokenable_id' => $user->id_auth,
    'name' => 'test_token',
    'token' => $tokenHash,
    'abilities' => '["*"]',
    'created_at' => now(),
    'updated_at' => now()
]);

echo "Created test token for user: {$token}\n\n";

// Create a request to the mobile API
$request = Request::create("/api/mobile/chat/messages/{$chat->pesanan_uuid}", 'GET');

// Set authorization header
$request->headers->set('Authorization', 'Bearer ' . $token);

// Manually set the authenticated user
$request->setUserResolver(function () use ($auth) {
    return $auth;
});

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