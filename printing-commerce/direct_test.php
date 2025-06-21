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

echo "=== Direct Testing of Chat Messages API ===\n\n";

// 1. Find a chat with messages
$chat = Chat::whereHas('messages')->first();

if (!$chat) {
    echo "No chats with messages found!\n";
    exit(1);
}

echo "Found chat: UUID={$chat->uuid}\n";
echo "Pesanan UUID: " . ($chat->pesanan_uuid ?? 'NULL') . "\n\n";

// 2. Find user for this chat
$user = null;
if ($chat->user_id) {
    $user = User::find($chat->user_id);
}

if (!$user) {
    $user = User::first();
    echo "Using first user since chat user not found.\n";
}

if (!$user) {
    echo "No users found in the database!\n";
    exit(1);
}

echo "User: {$user->nama_user} (ID: {$user->id_user}, Auth ID: {$user->id_auth})\n\n";

// 3. Create a personal access token for this user
$plainTextToken = bin2hex(random_bytes(32));
$hashedToken = hash('sha256', $plainTextToken);

DB::table('personal_access_tokens')->insert([
    'tokenable_type' => 'App\\Models\\Auth',
    'tokenable_id' => $user->id_auth,
    'name' => 'direct_test_token',
    'token' => $hashedToken,
    'abilities' => '["*"]',
    'created_at' => now(),
    'updated_at' => now()
]);

$tokenId = DB::getPdo()->lastInsertId();
$fullToken = $tokenId . '|' . $plainTextToken;

echo "Created token: $fullToken\n\n";

// 4. Get messages directly using the controller
echo "=== Testing with Controller ===\n\n";

// Create a request instance
$request = new \Illuminate\Http\Request();
$request->headers->set('Authorization', 'Bearer ' . $fullToken);

// Set the authenticated user
$auth = AuthModel::find($user->id_auth);
\Illuminate\Support\Facades\Auth::login($auth);

// Create controller instance
$controller = new \App\Http\Controllers\Mobile\ChatController();

// Call the method with the pesanan UUID if available
if ($chat->pesanan_uuid) {
    $response = $controller->getMessagesByPesanan($chat->pesanan_uuid);
    echo "Response from getMessagesByPesanan:\n";
    echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
    echo "\n\n";
}

// 5. Clean up
DB::table('personal_access_tokens')->where('id', $tokenId)->delete();
echo "Test token deleted\n\n";

echo "=== Testing Complete ===\n"; 