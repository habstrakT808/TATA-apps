<?php
namespace Database\Seeders;
use App\Models\Jasa;
use App\Models\PaketJasa;
use Illuminate\Database\Seeder;
Use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class JasaSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        // Cek paket jasa untuk jasa dengan ID 1 (Logo)
        $paketJasaLogo = PaketJasa::where('id_jasa', 1)->count();
        
        // Jika sudah ada paket jasa untuk ID 1, tidak perlu tambah lagi
        if ($paketJasaLogo > 0) {
            $this->command->info('Paket jasa untuk Logo sudah ada');
        } else {
            // Cek apakah jasa dengan ID 1 ada
            $jasa = Jasa::find(1);
            
            if (!$jasa) {
                // Jika tidak ada, buat jasa Logo baru
                $jasa = new Jasa();
                $jasa->uuid = Str::uuid();
                $jasa->kategori = 'logo';
                $jasa->deskripsi_jasa = 'Kami menawarkan jasa desain logo profesional untuk bisnis dan brand Anda.';
                $jasa->save();
                
                $this->command->info('Jasa Logo baru berhasil dibuat dengan ID: ' . $jasa->id_jasa);
            }
            
            $this->command->info('Menambahkan paket jasa untuk Logo (ID: ' . $jasa->id_jasa . ')');
            
            // Tambahkan paket jasa untuk Logo
            PaketJasa::create([
                'kelas_jasa' => 'basic',
                'deskripsi_singkat' => 'Paket basic untuk desain logo sederhana',
                'harga_paket_jasa' => 100000,
                'waktu_pengerjaan' => '3 hari',
                'maksimal_revisi' => 2,
                'id_jasa' => $jasa->id_jasa,
            ]);
            
            PaketJasa::create([
                'kelas_jasa' => 'standard',
                'deskripsi_singkat' => 'Paket standard untuk desain logo bisnis',
                'harga_paket_jasa' => 250000,
                'waktu_pengerjaan' => '5 hari',
                'maksimal_revisi' => 5,
                'id_jasa' => $jasa->id_jasa,
            ]);
            
            PaketJasa::create([
                'kelas_jasa' => 'premium',
                'deskripsi_singkat' => 'Paket premium untuk desain logo bisnis kompleks',
                'harga_paket_jasa' => 500000,
                'waktu_pengerjaan' => '7 hari',
                'maksimal_revisi' => 10,
                'id_jasa' => $jasa->id_jasa,
            ]);
            
            $this->command->info('Paket jasa untuk Logo berhasil ditambahkan');
        }
        
        // 2. Jasa Banner
        $jasa2 = Jasa::create([
            'uuid' => Str::uuid(),
            'kategori' => 'Banner',
            'deskripsi_jasa' => 'Kami menawarkan jasa desain banner untuk promosi dan iklan digital.',
        ]);
        
        // Paket-paket untuk Jasa Banner
        PaketJasa::create([
            'kelas_jasa' => 'basic',
            'deskripsi_singkat' => 'Paket basic untuk desain banner sederhana',
            'harga_paket_jasa' => 150000,
            'waktu_pengerjaan' => '3 hari',
            'maksimal_revisi' => 2,
            'id_jasa' => $jasa2->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'standard',
            'deskripsi_singkat' => 'Paket standard untuk desain banner profesional',
            'harga_paket_jasa' => 300000,
            'waktu_pengerjaan' => '5 hari',
            'maksimal_revisi' => 5,
            'id_jasa' => $jasa2->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'premium',
            'deskripsi_singkat' => 'Paket premium untuk desain banner eksklusif',
            'harga_paket_jasa' => 450000,
            'waktu_pengerjaan' => '7 hari',
            'maksimal_revisi' => 10,
            'id_jasa' => $jasa2->id_jasa,
        ]);
        
        // 3. Jasa Poster
        $jasa3 = Jasa::create([
            'uuid' => Str::uuid(),
            'kategori' => 'Poster',
            'deskripsi_jasa' => 'Kami menawarkan jasa desain poster untuk promosi dan acara.',
        ]);
        
        // Paket-paket untuk Jasa Poster
        PaketJasa::create([
            'kelas_jasa' => 'basic',
            'deskripsi_singkat' => 'Paket basic untuk desain poster sederhana',
            'harga_paket_jasa' => 100000,
            'waktu_pengerjaan' => '3 hari',
            'maksimal_revisi' => 2,
            'id_jasa' => $jasa3->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'standard',
            'deskripsi_singkat' => 'Paket standard untuk desain poster profesional',
            'harga_paket_jasa' => 200000,
            'waktu_pengerjaan' => '5 hari',
            'maksimal_revisi' => 5,
            'id_jasa' => $jasa3->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'premium',
            'deskripsi_singkat' => 'Paket premium untuk desain poster eksklusif',
            'harga_paket_jasa' => 350000,
            'waktu_pengerjaan' => '7 hari',
            'maksimal_revisi' => 10,
            'id_jasa' => $jasa3->id_jasa,
                ]);
        
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        if(!isset($jsonData['jasa'])){
            $jsonData['jasa'] = [];
        }
        $jsonData['jasa'] = array_merge($jsonData['jasa'], [$jasa2->id_jasa, $jasa3->id_jasa]);
        if(!isset($jsonData['paket_jasa'])){
            $jsonData['paket_jasa'] = [];
        }
        $jsonData['paket_jasa'] = array_merge($jsonData['paket_jasa'], PaketJasa::all()->pluck('id_paket_jasa')->toArray());
        file_put_contents(self::$tempFile,json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}