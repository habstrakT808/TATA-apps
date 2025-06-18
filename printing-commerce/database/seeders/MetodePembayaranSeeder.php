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
    private function dataSeeder() : array
    {
        return [
            [
                'uuid' => '9712fbe3-b51e-4b7e-95e6-33566021ed3b',
                'nama_metode_pembayaran' => 'BNI',
                'no_metode_pembayaran' => '973530284542',
                'deskripsi_1' => 'Aditya Tata',
                'deskripsi_2' => 'BNI',
                'thumbnail' => '1.jpg',
                'icon' => '1.jpeg',
            ],
            [
                'uuid' => 'e79fcffe-c7dd-4ac1-ac4b-4ef4faef5d37',
                'nama_metode_pembayaran' => 'MANDIRI',
                'no_metode_pembayaran' => '1357890235',
                'deskripsi_1' => 'Aditya Tata',
                'deskripsi_2' => 'MANDIRI',
                'thumbnail' => '1.jpg',
                'icon' => '1.jpeg',
            ],
            [
                'uuid' => 'cdfb5c3d-3726-4d1e-b887-3a81a690aa2f',
                'nama_metode_pembayaran' => 'OVO',
                'no_metode_pembayaran' => '087123456789',
                'deskripsi_1' => 'Aditya Tata',
                'deskripsi_2' => 'OVO',
                'thumbnail' => '1.jpg',
                'icon' => '1.jpeg',
            ],
        ];
    }
    public function run(): void
    {
        $ids = [];
        foreach($this->dataSeeder() as $mepe){
            $ids[] = MetodePembayaran::insertGetId($mepe);
            $destinationPath = public_path('img/mepe/' . $mepe['thumbnail']);
            $directory = dirname($destinationPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            copy(database_path('seeders/resources/img/mepe/' . $mepe['thumbnail']), $destinationPath);
        }
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        if(!isset($jsonData['metode_pembayaran'])){
            $jsonData['metode_pembayaran'] = [];
        }
        $jsonData['metode_pembayaran'] = array_merge($jsonData['metode_pembayaran'], $ids);
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}