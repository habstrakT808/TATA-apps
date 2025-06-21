<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "====== Admin Table Schema ======\n";
$adminSchema = DB::select('DESCRIBE admin');
foreach ($adminSchema as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

echo "\n====== Editor Table Schema ======\n";
$editorSchema = DB::select('DESCRIBE editor');
foreach ($editorSchema as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Key: {$column->Key}, Default: {$column->Default}\n";
}

$columns = DB::select('SHOW COLUMNS FROM metode_pembayaran');
print_r($columns); 