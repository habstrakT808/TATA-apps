<?php

// Load Laravel's database connection
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check users table
$userCount = DB::table('users')->count();
echo "Number of users in the database: " . $userCount . "\n";

// Check auth table for 'user' role
$userAuthCount = DB::table('auth')->where('role', 'user')->count();
echo "Number of user authentication records: " . $userAuthCount . "\n"; 