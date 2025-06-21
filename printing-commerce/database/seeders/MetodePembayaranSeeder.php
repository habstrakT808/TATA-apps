<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\MetodePembayaran;
use Illuminate\Support\Str;
class MetodePembayaranSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $metodePembayaran = [
            [
                'uuid' => Str::uuid(),
                'nama_metode' => 'Bank Mandiri',
                'jenis_metode' => 'bank_transfer',
                'no_metode_pembayaran' => '1234567890',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'is_active' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'nama_metode' => 'Bank BCA',
                'jenis_metode' => 'bank_transfer',
                'no_metode_pembayaran' => '0987654321',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'is_active' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'nama_metode' => 'Bank BRI',
                'jenis_metode' => 'bank_transfer',
                'no_metode_pembayaran' => '1122334455',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'is_active' => true,
            ],
        ];

        foreach ($metodePembayaran as $metode) {
            MetodePembayaran::create($metode);
        }
    }
}