<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\CatatanPesanan;
use App\Models\User;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Mobile\ChatController;

class PesananController extends Controller
{
    public function dirPath($idPesanan){
        if(env('APP_ENV', 'local') == 'local'){
            $path = public_path('assets3/img/pesanan/' . $idPesanan);
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            $path = base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/pesanan/' . $idPesanan;
        }
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        return $path;
    }

    public function getAll(Request $request)
    {
        try {
            // Check if user exists
            if (!$request->user() || !$request->user()->id_auth) {
                Log::error('User not authenticated or id_auth is null');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda perlu login kembali'
                ], 401);
            }
            
            // Get user ID safely
            $user = User::where('id_auth', $request->user()->id_auth)->first();
            if (!$user) {
                Log::error('User not found for auth ID: ' . $request->user()->id_auth);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan, silakan login kembali'
                ], 404);
            }
            $userId = $user->id_user;

            $pesanan = Pesanan::select(
                    'pesanan.id_pesanan', 
                    'pesanan.uuid', 
                    'pesanan.deskripsi', 
                    'pesanan.status_pesanan', 
                    'paket_jasa.harga_paket_jasa', 
                    'jasa.kategori', 
                    'paket_jasa.kelas_jasa', 
                    'pesanan.estimasi_waktu', 
                    'catatan_pesanan.gambar_referensi', 
                    'pesanan.maksimal_revisi', 
                    'pesanan.created_at', 
                    'pesanan.updated_at'
                )
                ->join('paket_jasa', 'paket_jasa.id_paket_jasa', '=', 'pesanan.id_paket_jasa')
                ->join('jasa', 'jasa.id_jasa', '=', 'paket_jasa.id_jasa')
                ->leftJoin('catatan_pesanan', 'catatan_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('pesanan.id_user', $userId)
                ->orderBy('pesanan.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil diambil',
                'data' => $pesanan,
                'status_pesanan' => ['pending', 'diproses', 'menunggu_editor', 'dibatalkan', 'selesai', 'dikerjakan', 'revisi'],
                'status_transaksi' => ['belum_bayar', 'menunggu_konfirmasi', 'lunas', 'dibatalkan', 'expired']
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving pesanan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil pesanan',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed pesanan information
     */
    public function getDetail(Request $request, $uuid)
    {
        try {
            // Check if user exists
            if (!$request->user() || !$request->user()->id_auth) {
                Log::error('User not authenticated or id_auth is null');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda perlu login kembali'
                ], 401);
            }
            
            // Get user ID safely
            $user = User::where('id_auth', $request->user()->id_auth)->first();
            if (!$user) {
                Log::error('User not found for auth ID: ' . $request->user()->id_auth);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan, silakan login kembali'
                ], 404);
            }
            $userId = $user->id_user;
            
            $pesanan = Pesanan::with([
                'fromPesananFile', 
                'fromCatatanPesanan', 
                'fromTransaksi.toMetodePembayaran',
                'toJasa',
                'toPaketJasa',
                'toEditor'
            ])
                ->where('uuid', $uuid)
                ->where('id_user', $userId)
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Detail Pesanan tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail pesanan berhasil diambil',
                'data' => $pesanan
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error retrieving detail pesanan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail pesanan',
                'data' => null
            ], 500);
        }
    }

    public function createPesananWithTransaction(Request $request){
        try {
            // Log untuk debugging
            Log::info('Pesanan request received', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate pesanan data
            $validator = Validator::make($request->all(), [
                'id_jasa' => 'required|exists:jasa,id_jasa',
                'id_paket_jasa' => 'required|exists:paket_jasa,id_paket_jasa',
                'catatan_user' => 'nullable|string|max:1000',
                'gambar_referensi' => 'nullable|file|mimes:jpeg,png,jpg|max:5120',
                'maksimal_revisi' => 'nullable|integer|min:0|max:5',
                'id_metode_pembayaran' => 'required|string'
            ], [
                'id_jasa.required' => 'Pilih jasa terlebih dahulu',
                'id_jasa.exists' => 'Jasa tidak valid',
                'id_paket_jasa.required' => 'Pilih paket jasa terlebih dahulu',
                'id_paket_jasa.exists' => 'Paket jasa tidak valid',
                'catatan_user.max' => 'Catatan revisi maksimal 1000 karakter',
                'gambar_referensi.mimes' => 'Format gambar harus jpeg, png, atau jpg',
                'gambar_referensi.max' => 'Ukuran gambar maksimal 5MB',
                'id_metode_pembayaran.required' => 'Metode pembayaran wajib dipilih'
            ]);
            
            if ($validator->fails()) {
                Log::error('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'status' => 'error', 
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            // Check if user exists
            if (!$request->user() || !$request->user()->id_auth) {
                Log::error('User not authenticated');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda perlu login kembali'
                ], 401);
            }
            
            // Get user ID safely
            $user = User::where('id_auth', $request->user()->id_auth)->first();
            if (!$user) {
                Log::error('User not found for auth ID: ' . $request->user()->id_auth);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan, silakan login kembali'
                ], 404);
            }
            $userId = $user->id_user;
            
            // Get jasa and paket details for pricing
            $jasa = Jasa::find($request->input('id_jasa'));
            $paketJasa = PaketJasa::find($request->input('id_paket_jasa'));
            if (!$jasa || !$paketJasa) {
                Log::error('Jasa or PaketJasa not found', [
                    'id_jasa' => $request->input('id_jasa'),
                    'id_paket_jasa' => $request->input('id_paket_jasa')
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jasa atau paket tidak ditemukan'
                ], 404);
            }

            // Get metode pembayaran - PERBAIKI BAGIAN INI
            $metodePembayaranUuid = $request->input('id_metode_pembayaran');
            
            \Log::info('Debug metode pembayaran', [
                'id_metode_pembayaran' => $metodePembayaranUuid,
                'all_metode' => MetodePembayaran::all()->toArray()
            ]);
            
            \Log::info('Looking for metode pembayaran', [
                'uuid' => $metodePembayaranUuid,
                'available_methods' => MetodePembayaran::all()->pluck('id_metode_pembayaran', 'nama_metode_pembayaran')->toArray()
            ]);
            
            $metodePembayaran = MetodePembayaran::where('uuid', $metodePembayaranUuid)->first();
            
            if (!$metodePembayaran) {
                // Coba cari berdasarkan ID jika UUID tidak ditemukan
                \Log::info('Metode pembayaran not found by UUID, trying by ID');
                $metodePembayaran = MetodePembayaran::find($metodePembayaranUuid);
                
                // Jika masih tidak ditemukan, ambil metode pembayaran pertama (temporary fix)
                if (!$metodePembayaran) {
                    \Log::info('Metode pembayaran not found by ID, using first available method');
                    $metodePembayaran = MetodePembayaran::first();
                    
                    if (!$metodePembayaran) {
                        \Log::error('No metode pembayaran available in database');
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Tidak ada metode pembayaran tersedia'
                        ], 404);
                    }
                }
            }

            // Calculate total price and estimation
            $estimasiWaktu = Carbon::now();
            $jumlahRevisi = $request->input('maksimal_revisi') ?? $paketJasa->maksimal_revisi;
            $uuid = Str::uuid();

            // Begin transaction
            DB::beginTransaction();
            try {
                // Create pesanan
                $idPesanan = Pesanan::insertGetId([
                    'uuid' => $uuid,
                    'deskripsi' => $request->catatan_user,
                    'status_pesanan' => 'pending',
                    'total_harga' => $paketJasa->harga_paket_jasa,
                    'estimasi_waktu' => $estimasiWaktu,
                    'maksimal_revisi' => $jumlahRevisi,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'id_user' => $userId,
                    'id_jasa' => $request->input('id_jasa'),
                    'id_paket_jasa' => $request->input('id_paket_jasa')
                ]);
                
                // Create directory for order files
                try {
                    $orderPath = $this->dirPath($uuid);
                    if (!file_exists($orderPath)) {
                        mkdir($orderPath, 0755, true);
                    }
                } catch (\Exception $e) {
                    Log::error('Error creating order directory: ' . $e->getMessage());
                    // Continue without failing if directory creation fails
                }
                
                $filename = null;
                if ($request->hasFile('gambar_referensi') && $request->file('gambar_referensi')->isValid() && in_array($request->file('gambar_referensi')->extension(), ['jpeg', 'png', 'jpg'])) {
                    $file = $request->file('gambar_referensi');
                    $filename = $file->hashName();
                    
                    try {
                        $catatanPath = $this->dirPath($uuid . '/catatan_pesanan');
                        if (!file_exists($catatanPath)) {
                            mkdir($catatanPath, 0755, true);
                        }
                        $file->move($catatanPath, $filename);
                    } catch (\Exception $e) {
                        Log::error('Error handling reference image: ' . $e->getMessage());
                        // Continue without failing if file handling fails
                    }
                }
                
                // Create catatan pesanan record
                if($request->input('catatan_user') != null && $request->input('catatan_user') != ''){
                    CatatanPesanan::create([
                        'catatan_pesanan' => $request->input('catatan_user'),
                        'gambar_referensi' => $filename,
                        'uploaded_at' => now(),
                        'id_pesanan' => $idPesanan,
                        'id_user' => $userId
                    ]);
                }
                
                // Generate unique order ID for transaction
                $orderId = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(8));
                
                // Set expiration time (24 hours from now)
                $expiredAt = Carbon::now()->addHours(24);
                
                // Create transaction
                $transaksi = Transaksi::create([
                    'order_id' => $orderId,
                    'jumlah' => $paketJasa->harga_paket_jasa,
                    'status_transaksi' => 'belum_bayar',
                    'bukti_pembayaran' => null,
                    'waktu_pembayaran' => null,
                    'expired_at' => $expiredAt,
                    'id_metode_pembayaran' => $metodePembayaran->id_metode_pembayaran,
                    'id_pesanan' => $idPesanan
                ]);
                
                // Create a chat room for this order
                try {
                    $chatController = new ChatController();
                    $chat = $chatController->createChatForOrder($uuid, $userId);
                } catch (\Exception $e) {
                    Log::error('Error creating chat for order: ' . $e->getMessage());
                    // Create a default chat object to avoid null reference
                    $chat = (object) [
                        'uuid' => null,
                        'admin' => null
                    ];
                }
                
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesanan dan transaksi berhasil dibuat. Silakan lakukan pembayaran dan upload bukti transfer.',
                    'data' => [
                        'id_pesanan' => $uuid,
                        'transaksi' => $transaksi,
                        'payment_method' => $metodePembayaran,
                        'chat' => [
                            'chat_uuid' => $chat->uuid,
                            'admin_name' => $chat->admin ? $chat->admin->nama_admin : 'Admin TATA'
                        ],
                        'expired_at' => $expiredAt->format('Y-m-d H:i:s'),
                        'payment_instructions' => [
                            'step1' => 'Transfer ke rekening: ' . $metodePembayaran->no_metode_pembayaran,
                            'step2' => 'Atas nama: ' . $metodePembayaran->deskripsi_1,
                            'step2-2' => 'Atas nama: ' . $metodePembayaran->deskripsi_2,
                            'step3' => 'Nominal: Rp ' . number_format($paketJasa->harga_paket_jasa, 0, ',', '.'),
                            'step4' => 'Upload bukti transfer untuk konfirmasi'
                        ]
                    ]
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in transaction: ' . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membuat pesanan dan transaksi: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error creating order with transaction: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pesanan dan transaksi',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel pesanan (only if pending)
     */
    public function cancel(Request $request){
        try {
            $validator = Validator::make($request->only('id_pesanan'), [
                'id_pesanan' => 'required',
            ], [
                'id_pesanan.required' => 'ID pesanan wajib di isi',
            ]);
            if ($validator->fails()){
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            
            // Check if user exists
            if (!$request->user() || !$request->user()->id_auth) {
                Log::error('User not authenticated or id_auth is null');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda perlu login kembali'
                ], 401);
            }
            
            // Get user ID safely
            $user = User::where('id_auth', $request->user()->id_auth)->first();
            if (!$user) {
                Log::error('User not found for auth ID: ' . $request->user()->id_auth);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan, silakan login kembali'
                ], 404);
            }
            $userId = $user->id_user;
            
            $pesanan = Pesanan::join('catatan_pesanan', 'catatan_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('uuid', $request->input('id_pesanan'))
                ->where('pesanan.id_user', $userId)
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            if ($pesanan->status_pesanan == 'dibatalkan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan sudah dibatalkan'
                ], 422);
            }
            if ($pesanan->status_pesanan != 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak dapat dibatalkan pada status ini'
                ], 422);
            }
            if($pesanan->gambar_referensi != null){
                Storage::disk('pesanan')->delete('catatan_pesanan/' . $pesanan->gambar_referensi);
            }
            // Cancel related transactions
            Transaksi::where('id_pesanan', $pesanan->id_pesanan)
                ->update(['status_transaksi' => 'dibatalkan']);

            $pesanan->update([
                'status_pesanan' => 'dibatalkan',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error canceling order: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan pesanan',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order info for chat - simplified version
     */
    public function getOrderInfo(Request $request, $uuid)
    {
        try {
            // Check if user exists
            if (!$request->user() || !$request->user()->id_auth) {
                Log::error('User not authenticated or id_auth is null');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda perlu login kembali'
                ], 401);
            }
            
            // Get user ID safely
            $user = User::where('id_auth', $request->user()->id_auth)->first();
            if (!$user) {
                Log::error('User not found for auth ID: ' . $request->user()->id_auth);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan, silakan login kembali'
                ], 404);
            }
            $userId = $user->id_user;
            
            // Strategy untuk mencari pesanan (sama seperti di ChatController)
            $pesanan = null;
            
            // 1. Coba langsung sebagai UUID pesanan
            $pesanan = Pesanan::where('uuid', $uuid)
                ->where('id_user', $userId)
                ->with(['toJasa', 'toPaketJasa', 'fromTransaksi.toMetodePembayaran'])
                ->first();
            
            // 2. Jika tidak ketemu, coba cari di transaksi
            if (!$pesanan) {
                $transaksi = Transaksi::where('order_id', $uuid)->first();
                if ($transaksi) {
                    $pesanan = Pesanan::where('uuid', $transaksi->pesanan_uuid)
                        ->where('id_user', $userId)
                        ->with(['toJasa', 'toPaketJasa', 'fromTransaksi.toMetodePembayaran'])
                        ->first();
                }
            }
            
            // 3. Jika masih tidak ketemu, coba partial match
            if (!$pesanan) {
                $pesanan = Pesanan::where('uuid', 'like', "%$uuid%")
                    ->where('id_user', $userId)
                    ->with(['toJasa', 'toPaketJasa', 'fromTransaksi.toMetodePembayaran'])
                    ->first();
            }
            
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Format response untuk chat info box
            $orderInfo = [
                'order_id' => $uuid,
                'pesanan_uuid' => $pesanan->uuid,
                'jasa' => [
                    'kategori' => $pesanan->toJasa ? ucfirst($pesanan->toJasa->kategori) : 'Logo',
                    'nama_jasa' => $pesanan->toJasa ? $pesanan->toJasa->nama_jasa : 'Desain Logo',
                ],
                'paket' => [
                    'kelas_jasa' => $pesanan->toPaketJasa ? ucfirst($pesanan->toPaketJasa->kelas_jasa) : 'Premium',
                    'harga' => $pesanan->toPaketJasa ? $pesanan->toPaketJasa->harga_paket_jasa : 0,
                ],
                'metode_pembayaran' => 'Virtual Account Mandiri', // Default
                'status_pesanan' => $pesanan->status_pesanan,
                'created_at' => $pesanan->created_at,
            ];
            
            // Jika ada transaksi, ambil metode pembayaran yang sebenarnya
            if ($pesanan->fromTransaksi && $pesanan->fromTransaksi->isNotEmpty()) {
                $transaksi = $pesanan->fromTransaksi->first();
                if ($transaksi && $transaksi->toMetodePembayaran) {
                    $orderInfo['metode_pembayaran'] = $transaksi->toMetodePembayaran->nama_metode_pembayaran;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Info pesanan berhasil diambil',
                'data' => $orderInfo
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error getting order info: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil info pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}