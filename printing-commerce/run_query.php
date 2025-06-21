<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking recent messages in chat ===\n";
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