<?php
// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Query 1: Cek admin dengan ID 65 ===\n";
$admin65 = DB::select('SELECT a.id_admin, a.nama_admin, au.id_auth, au.email, au.role 
                      FROM admin a 
                      JOIN auth au ON a.id_auth = au.id_auth 
                      WHERE a.id_admin = 65');
print_r($admin65);

echo "\n=== Query 2: Cek admin yang login di web (adminchat@gmail.com) ===\n";
$adminChat = DB::select('SELECT a.id_admin, a.nama_admin, au.id_auth, au.email, au.role 
                        FROM admin a 
                        JOIN auth au ON a.id_auth = au.id_auth 
                        WHERE au.email = ?', ['adminchat@gmail.com']);
print_r($adminChat);

echo "\n=== Query 3: Cek chat yang ada (5 teratas) ===\n";
$chats = DB::select('SELECT c.id, c.uuid, c.user_id, c.admin_id, c.pesanan_uuid, c.last_message,
                    u.nama_user, a.nama_admin
                    FROM chats c
                    LEFT JOIN users u ON c.user_id = u.id_user  
                    LEFT JOIN admin a ON c.admin_id = a.id_admin
                    ORDER BY c.updated_at DESC
                    LIMIT 5');
print_r($chats); 