<?php
// test-metode-pembayaran.php
// Run this script with: php test-metode-pembayaran.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MetodePembayaran;

echo "=== TESTING METODE PEMBAYARAN ===\n\n";

// Check table structure
echo "Checking table structure...\n";
$columns = Schema::getColumnListing('metode_pembayaran');
echo "Table columns: " . implode(', ', $columns) . "\n\n";

// Check for specific columns
echo "Checking specific columns...\n";
echo "nama_metode exists: " . (Schema::hasColumn('metode_pembayaran', 'nama_metode') ? 'YES' : 'NO') . "\n";
echo "nama_metode_pembayaran exists: " . (Schema::hasColumn('metode_pembayaran', 'nama_metode_pembayaran') ? 'YES' : 'NO') . "\n";
echo "is_active exists: " . (Schema::hasColumn('metode_pembayaran', 'is_active') ? 'YES' : 'NO') . "\n\n";

// Check existing data
echo "Checking existing data...\n";
$methods = DB::table('metode_pembayaran')->get();
echo "Found " . count($methods) . " payment methods\n\n";

// Display each method
foreach ($methods as $index => $method) {
    echo "Method #" . ($index + 1) . ":\n";
    foreach ((array)$method as $key => $value) {
        echo "  $key: $value\n";
    }
    echo "\n";
}

// Test the model
echo "Testing the model...\n";
try {
    $modelMethods = MetodePembayaran::all();
    echo "Model found " . $modelMethods->count() . " payment methods\n\n";
    
    foreach ($modelMethods as $index => $method) {
        echo "Model Method #" . ($index + 1) . ":\n";
        echo "  UUID: " . $method->uuid . "\n";
        
        // Check which name attribute exists
        if (Schema::hasColumn('metode_pembayaran', 'nama_metode_pembayaran')) {
            echo "  Name: " . $method->nama_metode_pembayaran . "\n";
        } elseif (Schema::hasColumn('metode_pembayaran', 'nama_metode')) {
            echo "  Name: " . $method->nama_metode . "\n";
        }
        
        echo "  No: " . $method->no_metode_pembayaran . "\n";
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error testing model: " . $e->getMessage() . "\n";
}

echo "=== TEST COMPLETE ===\n"; 