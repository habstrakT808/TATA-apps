<?php

// Load Laravel framework
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\JasaImage;
use App\Models\Pesanan;

echo "Starting duplicate jasa cleanup...\n";

// IDs of the duplicate services to delete
$duplicateIds = [4, 5];

// Begin transaction
DB::beginTransaction();

try {
    // Check if there are any orders referencing these services
    $orderCount = Pesanan::whereIn('id_jasa', $duplicateIds)->count();
    if ($orderCount > 0) {
        echo "Error: Found {$orderCount} orders referencing these services. Cannot delete.\n";
        DB::rollBack();
        exit(1);
    }
    
    // Delete paket_jasa entries for the duplicate services
    $deletedPaketCount = PaketJasa::whereIn('id_jasa', $duplicateIds)->delete();
    echo "Deleted {$deletedPaketCount} paket jasa entries.\n";
    
    // Delete jasa_images entries for the duplicate services (if any)
    $deletedImageCount = JasaImage::whereIn('id_jasa', $duplicateIds)->delete();
    echo "Deleted {$deletedImageCount} jasa image entries.\n";
    
    // Delete the duplicate jasa entries
    $deletedJasaCount = Jasa::whereIn('id_jasa', $duplicateIds)->delete();
    echo "Deleted {$deletedJasaCount} duplicate jasa entries.\n";
    
    // Commit transaction
    DB::commit();
    echo "Cleanup completed successfully!\n";
    
} catch (\Exception $e) {
    // Rollback transaction on error
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Verify remaining jasa entries
$remainingJasa = Jasa::all();
echo "\nRemaining jasa entries:\n";
foreach ($remainingJasa as $jasa) {
    echo "- ID: {$jasa->id_jasa}, Kategori: {$jasa->kategori}, UUID: {$jasa->uuid}\n";
}

echo "\nCleanup script completed.\n"; 