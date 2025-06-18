<?php
namespace Database\Seeders;
use App\Models\CatatanPesanan;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
class PesananCatatanSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        $briefTexts = [
            'Saya membutuhkan desain logo untuk perusahaan teknologi. Logo harus modern, minimalis, dan mencerminkan inovasi. Warna yang diinginkan adalah biru dan putih.',
            'Butuh banner untuk promosi produk makanan. Ukuran A3, dengan foto produk yang menarik dan informasi promo. Tema warna hangat seperti orange dan merah.',
            'Desain kartu nama untuk bisnis konsultan. Desain profesional, elegan, dengan informasi kontak lengkap. Preferensi warna hitam dan emas.',
            'Poster event musik untuk konser indie. Desain harus eye-catching, dengan informasi tanggal, tempat, dan lineup artis. Tema vintage atau retro.',
            'Flyer untuk grand opening restoran. Desain appetizing dengan foto makanan, promo opening, dan alamat lengkap. Warna sesuai branding restoran.',
            'Logo untuk startup fintech. Konsep trust, security, dan innovation. Warna biru navy atau hijau. Harus scalable untuk berbagai media.',
            'Desain kemasan produk skincare. Minimalis, clean, target market wanita 20-35 tahun. Warna soft pastel, dengan informasi produk yang jelas.',
            'Banner website untuk agency digital marketing. Header yang menarik, dengan call-to-action yang kuat. Responsive design untuk mobile dan desktop.',
            'Desain merchandise kaos untuk brand clothing. Artwork yang unik, target anak muda. Bisa screen printing, maksimal 3 warna.',
            'Infografis untuk presentasi bisnis. Data visualization yang mudah dipahami, professional look, dengan chart dan diagram yang menarik.'
        ];
        $sampleImages = [
            'sample_logo_ref.jpg',
            'sample_banner_ref.png', 
            'sample_card_ref.jpg',
            'sample_poster_ref.png',
            'sample_flyer_ref.jpg'
        ];
        $pesananList = Pesanan::select('id_pesanan', 'id_user')->get();
        for($i = 1; $i <= 10; $i++){
            $pesanan = $pesananList[rand(0, 9)];
            $idPesanan = $pesanan->id_pesanan;
            $idUser = $pesanan->id_user;
            $briefText = $briefTexts[rand(0, count($briefTexts) - 1)];
            $hasImage = rand(1, 10) <= 7;
            $imageName = $hasImage ? $sampleImages[rand(0, count($sampleImages) - 1)] : null;
            $idCatatanPesanan = CatatanPesanan::insertGetId([
                'catatan_pesanan' => $briefText,
                'gambar_referensi' => $imageName,
                'id_pesanan' => $idPesanan,
                'id_user' => $idUser
            ]);
            $idCatatanPesanans[] = $idCatatanPesanan;
        }
        if(!isset($jsonData['catatan_pesanan'])){
            $jsonData['catatan_pesanan'] = [];
        }
        $jsonData['catatan_pesanan'] = array_merge($jsonData['catatan_pesanan'], $idCatatanPesanans);
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}