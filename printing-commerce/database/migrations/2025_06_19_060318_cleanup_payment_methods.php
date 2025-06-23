<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, delete all existing payment methods
        DB::table('metode_pembayaran')->delete();

        // Create the three payment methods with proper UUIDs
        $paymentMethods = [
            [
                'uuid' => Str::uuid()->toString(),
                'nama_metode_pembayaran' => 'BNI',
                'no_metode_pembayaran' => '123456789',
                'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
                'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
                'thumbnail' => 'bni.jpg',
                'icon' => 'bni-icon.png'
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'nama_metode_pembayaran' => 'Mandiri',
                'no_metode_pembayaran' => '987654321',
                'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
                'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
                'thumbnail' => 'mandiri.jpg',
                'icon' => 'mandiri-icon.png'
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'nama_metode_pembayaran' => 'OVO',
                'no_metode_pembayaran' => '081234567890',
                'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
                'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
                'thumbnail' => 'ovo.jpg',
                'icon' => 'ovo-icon.png'
            ]
        ];

        // Insert each payment method individually to ensure all are added
        foreach ($paymentMethods as $method) {
            DB::table('metode_pembayaran')->insert($method);
        }
        
        // Print the payment methods to confirm
        $methods = DB::table('metode_pembayaran')->select('nama_metode_pembayaran', 'no_metode_pembayaran')->get();
        echo "Payment methods updated successfully!\n";
        foreach ($methods as $method) {
            echo "- " . $method->nama_metode_pembayaran . " (" . $method->no_metode_pembayaran . ")\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore original data
    }
};
