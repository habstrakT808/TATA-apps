<?php
namespace Database\Seeders;
use App\Models\Transaksi;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
class TransaksiSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        $ids = [];

        // Create sample bukti pembayaran files
        $this->createSamplePaymentProofs();

        // Get pesanan that need transactions (only certain status)
        $pesananNeedingTransactions = Pesanan::whereIn('status_pesanan', [
            'menunggu_editor',  // waiting for payment confirmation
            'dikerjakan',           // already paid, being worked
            'revisi',              // already paid, in revision
            'selesai'              // already paid, completed
        ])
        ->get();

        foreach ($pesananNeedingTransactions as $pesanan) {
            $createdAt = $pesanan->created_at;
            $now = Carbon::now();
            
            // Determine transaction status based on pesanan status
            $transaksiStatus = 'menunggu_konfirmasi';
            $waktuPembayaran = null;
            $confirmedAt = null;
            $buktiPembayaran = null;
            
            if ($pesanan->status_pesanan === 'menunggu_konfirmasi') {
                // User has uploaded payment proof, waiting admin confirmation
                $transaksiStatus = 'menunggu_konfirmasi';
                $waktuPembayaran = $createdAt->copy()->addHours(rand(1, 24));
                $buktiPembayaran = 'payment_proof_' . rand(1, 5) . '.jpg';
            } else {
                // Payment already confirmed
                $transaksiStatus = 'lunas';
                $waktuPembayaran = $createdAt->copy()->addHours(rand(1, 24));
                $confirmedAt = $pesanan->confirmed_at ?: $waktuPembayaran->copy()->addHours(rand(1, 12));
                $buktiPembayaran = 'payment_proof_' . rand(1, 5) . '.jpg';
            }

            // Generate realistic order ID
            $orderId = 'ORD-' . $createdAt->format('Ymd') . '-' . strtoupper(Str::random(6));
            
            // Set expiration (24 hours from order creation)
            $expiredAt = $createdAt->copy()->addHours(24);
            
            $transaksiId = Transaksi::insertGetId([
                'order_id' => $orderId,
                'jumlah' => $pesanan->total_harga,
                'status_transaksi' => $transaksiStatus,
                'bukti_pembayaran' => $buktiPembayaran,
                'waktu_pembayaran' => $waktuPembayaran,
                'confirmed_at' => $confirmedAt,
                'catatan_transaksi' => $confirmedAt ? 'Pembayaran sudah dikonfirmasi, pesanan bisa dilanjutkan' : null,
                'expired_at' => $expiredAt,
                'id_metode_pembayaran' => $jsonData['metode_pembayaran'][rand(0, min(2, count($jsonData['metode_pembayaran'])-1))],
                'id_pesanan' => $pesanan->id_pesanan,
                'created_at' => $createdAt,
                'updated_at' => $confirmedAt ?: $waktuPembayaran ?: $createdAt
            ]);
            
            $ids[] = $transaksiId;
        }

        // Create some expired transactions for testing
        $expiredPesanan = Pesanan::where('status_pesanan', 'pending')->take(3)->get();

        foreach ($expiredPesanan as $pesanan) {
            $createdAt = $pesanan->created_at;
            $orderId = 'ORD-' . $createdAt->format('Ymd') . '-' . strtoupper(Str::random(6));
            $expiredAt = $createdAt->copy()->addHours(24);
            
            $transaksiId = Transaksi::insertGetId([
                'order_id' => $orderId,
                'jumlah' => $pesanan->total_harga,
                'status_transaksi' => 'expired',
                'bukti_pembayaran' => null,
                'waktu_pembayaran' => null,
                'confirmed_at' => null,
                'catatan_transaksi' => null,
                'expired_at' => $expiredAt,
                'id_metode_pembayaran' => $jsonData['metode_pembayaran'][rand(0, min(2, count($jsonData['metode_pembayaran'])-1))],
                'id_pesanan' => $pesanan->id_pesanan,
                'created_at' => $createdAt,
                'updated_at' => $expiredAt
            ]);
            
            $ids[] = $transaksiId;
        }

        // Save to temp file
        if(!isset($jsonData['transaksi'])){
            $jsonData['transaksi'] = [];
        }
        $jsonData['transaksi'] = array_merge($jsonData['transaksi'], $ids);
        file_put_contents(self::$tempFile, json_encode($jsonData, JSON_PRETTY_PRINT));
    }

    /**
     * Create sample payment proof images
     */
    private function createSamplePaymentProofs()
    {
        $sampleFiles = [
            'payment_proof_1.jpg',
            'payment_proof_2.jpg', 
            'payment_proof_3.jpg',
            'payment_proof_4.jpg',
            'payment_proof_5.jpg'
        ];

        // Create sample payment proof files if they don't exist
        foreach ($sampleFiles as $file) {
            if (!file_exists($file)) {
                // Create a simple placeholder file
                $content = "Sample payment proof for testing - $file";
                file_put_contents($file, $content);
            }
        }
    }
}