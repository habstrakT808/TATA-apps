<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Get raw data from database
echo "=== Raw Database Output ===\n\n";

$messages = DB::select('
    SELECT cm.*, 
           CASE 
               WHEN cm.sender_type = "admin" THEN a.nama_admin
               WHEN cm.sender_type = "user" THEN u.nama_user
               ELSE "Unknown"
           END as sender_name
    FROM chat_messages cm
    LEFT JOIN admin a ON cm.sender_type = "admin" AND cm.sender_id = a.id_auth
    LEFT JOIN users u ON cm.sender_type = "user" AND u.id_auth = cm.sender_id
    WHERE cm.chat_uuid = "65642798-3e79-42ea-89a1-d124ab0d1994"
    ORDER BY cm.created_at DESC
    LIMIT 10;
');

print_r($messages);

// 2. Get raw data from API response
echo "\n\n=== Raw API Response ===\n\n";

// Find a user with the correct ID
$user = DB::table('users')->where('id_user', 110)->first();
if (!$user) {
    echo "User with ID 110 not found!\n";
    exit(1);
}

echo "Found user with ID 110: " . ($user->nama_user ?? 'Unknown') . "\n";
echo "Auth ID: " . ($user->id_auth ?? 'NULL') . "\n\n";

// Find the auth record
$auth = DB::table('auth')->where('id_auth', $user->id_auth)->first();
if (!$auth) {
    echo "Auth record not found for user!\n";
    exit(1);
}

echo "Found auth record with email: " . $auth->email . "\n\n";

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->merge(['chat_uuid' => '65642798-3e79-42ea-89a1-d124ab0d1994']);

// Mock the Auth facade
\Illuminate\Support\Facades\Auth::shouldReceive('id')
    ->andReturn($user->id_auth);

// Call the controller method
$controller = new \App\Http\Controllers\Mobile\ChatController();
$response = $controller->getMessages($request);

// Output response
echo "Response Content:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
echo "\n\n=== End of Output ===\n"; 