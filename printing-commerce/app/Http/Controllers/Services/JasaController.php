<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Admin;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\JasaImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class JasaController extends Controller
{
    private function dirPath($kategori){
        if(env('APP_ENV', 'local') == 'local'){
            return public_path('assets3/img/jasa/' . $kategori);
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            return base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/jasa/' . $kategori;
        }
    }
    public function updateJasa(Request $rt){
        // Debug logging
        Log::info('Update Jasa request received', [
            'has_images' => $rt->hasFile('images'),
            'image_count' => $rt->hasFile('images') ? count($rt->file('images')) : 0,
            'has_deleted_images' => $rt->has('deleted_images'),
            'deleted_images' => $rt->input('deleted_images')
        ]);
        
        $vJasa = Validator::make($rt->only('id_jasa', 'images', 'kelas_jasa', 'deskripsi_jasa', 'harga_paket_jasa', 'waktu_pengerjaan', 'maksimal_revisi', 'deskripsi_singkat', 'deleted_images'), [
            'id_jasa' => 'required',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'kelas_jasa' => 'required|in:basic,standard,premium',
            'deskripsi_jasa' => 'required|max:500',
            'harga_paket_jasa' => 'required|integer',
            'waktu_pengerjaan' => 'required',
            'maksimal_revisi' => 'required|integer|max:20',
            'deskripsi_singkat' => 'required|max:300',
            'deleted_images' => 'nullable|string',
        ], [
            'id_jasa.required' => 'ID Jasa wajib di isi',
            'images.*.image' => 'Galeri Jasa harus berupa gambar',
            'images.*.mimes' => 'Format Galeri Jasa tidak valid. Gunakan format jpeg, png, jpg',
            'images.*.max' => 'Ukuran Galeri Jasa tidak boleh lebih dari 5MB',
            'kelas_jasa.required' => 'Kelas Jasa wajib di isi',
            'kelas_jasa.in' => 'Kelas Jasa harus Basic, Standard, atau Premium',
            'deskripsi_jasa.required' => 'Deskripsi Jasa wajib di isi',
            'deskripsi_jasa.max' => 'Deskripsi Jasa maksimal 500 karakter',
            'harga_paket_jasa.required' => 'Harga Paket Jasa wajib di isi',
            'harga_paket_jasa.integer'=>'Harga Paket Jasa harus berupa angka',
            'waktu_pengerjaan.required' => 'Waktu Pengerjaan wajib di isi',
            'maksimal_revisi.required' => 'Maksimal Revisi wajib di isi',
            'maksimal_revisi.integer'=>'Maksimal Revisi harus berupa angka',
            'maksimal_revisi.max'=>'Maksimal Revisi maksimal 20',
            'deskripsi_singkat.required' => 'Deskripsi Singkat wajib di isi',
            'deskripsi_singkat.max' => 'Deskripsi Singkat maksimal 300 karakter',
            'deleted_images.string' => 'Deleted Image harus String',
        ]);
        
        if ($vJasa->fails()){
            $errors = [];
            foreach ($vJasa->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        $jasa = Jasa::where('uuid', $rt->input('id_jasa'))->first();
        if(!$jasa){
            return response()->json(['status' => 'error', 'message' => 'Data Jasa tidak ditemukan'], 404);
        }
        $paketJasa = PaketJasa::where('id_jasa', $jasa->id_jasa)->where('kelas_jasa', $rt->input('kelas_jasa'))->first();
        if(!$paketJasa){
            return response()->json(['status' => 'error', 'message' => 'Data Paket Jasa tidak ditemukan'], 404);
        }
        // Update jasa description
        $jasa->update([
            'deskripsi_jasa' => $rt->input('deskripsi_jasa')
        ]);
        $waktuPengerjaan = $rt->input('waktu_pengerjaan');
        if (empty($waktuPengerjaan)) {
            $waktuPengerjaan = '7 hari';
        }
        $existingImagesCount = JasaImage::where('id_jasa', $jasa->id_jasa)->count();
        $deletedImagesCount = 0;
        if ($rt->has('deleted_images') && !empty($rt->input('deleted_images'))) {
            $deletedImages = json_decode($rt->input('deleted_images'), true);
            $deletedImagesCount = is_array($deletedImages) ? count($deletedImages) : 1;
        }
        $newImagesCount = $rt->hasFile('images') ? count($rt->file('images')) : 0;
        $finalImageCount = $existingImagesCount - $deletedImagesCount + $newImagesCount;
        
        if ($finalImageCount <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Jasa harus memiliki minimal 1 gambar'], 400);
        }
        
        if ($finalImageCount > 5) {
            return response()->json(['status' => 'error', 'message' => 'Maksimal 5 gambar untuk setiap jasa'], 400);
        }
        // Process deleted images
        if ($rt->has('deleted_images') && !empty($rt->input('deleted_images'))) {
            $deletedImages = json_decode($rt->input('deleted_images'), true);
            if (is_array($deletedImages)) {
                foreach ($deletedImages as $imageId) {
                    $image = JasaImage::find($imageId);
                    if ($image) {
                        $imagePath = $this->dirPath($jasa->kategori) . '/' . $image->image_path;
                        if (file_exists($imagePath) && !is_dir($imagePath)) {
                            unlink($imagePath);
                        }
                        $image->delete();
                    }
                }
            } else {
                $image = JasaImage::find($rt->input('deleted_images'));
                if ($image) {
                    $imagePath = $this->dirPath($jasa->kategori) . '/' . $image->image_path;
                    if (file_exists($imagePath) && !is_dir($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }
            }
        }
        // Process new images
        if ($rt->hasFile('images')) {
            // Make sure the directory exists
            $targetDir = $this->dirPath($jasa->kategori);
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            foreach ($rt->file('images') as $image) {
                if ($image->isValid() && in_array($image->extension(), ['jpeg', 'png', 'jpg'])) {
                    try {
                        $imageName = $image->hashName();
                        $image->move($targetDir, $imageName);
                        
                        JasaImage::create([
                            'image_path' => $imageName,
                            'id_jasa' => $jasa->id_jasa,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error uploading image: ' . $e->getMessage());
                        return response()->json(['status' => 'error', 'message' => 'Gagal mengupload gambar'], 500);
                    }
                }
            }
        }
        
        // Update paket jasa data
        $paketJasa->update([
            'harga_paket_jasa' => $rt->input('harga_paket_jasa'),
            'waktu_pengerjaan' => $waktuPengerjaan,
            'maksimal_revisi' => $rt->input('maksimal_revisi'),
            'deskripsi_singkat' => $rt->input('deskripsi_singkat'),
        ]);
        
        return response()->json(['status'=>'success','message'=>'Data Jasa berhasil diupdate']);
    }

    /**
     * Get Jasa by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJasaById($id)
    {
        try {
            // Log untuk debugging
            Log::info('Getting jasa by ID', ['id' => $id]);
            
            // Find jasa by ID
            $jasa = Jasa::find($id);
            
            if (!$jasa) {
                Log::error('Jasa not found', ['id' => $id]);
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Service not found',
                    'data' => null
                ], 404);
            }
            
            Log::info('Jasa found', ['jasa' => $jasa->toArray()]);
            
            // Get paket jasa for this jasa
            $paketJasa = PaketJasa::where('id_jasa', $jasa->id_jasa)->get();
            
            Log::info('Paket jasa count', ['count' => $paketJasa->count()]);
            
            if ($paketJasa->count() == 0) {
                Log::warning('No paket jasa found for jasa', ['id_jasa' => $jasa->id_jasa]);
            }
            
            // Format response
            $data = [
                'jasa' => $jasa,
                'paket_jasa' => $paketJasa
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Error getting jasa', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function createJasa(Request $rt)
    {
        // Validasi input
        $vJasa = Validator::make($rt->only('kategori', 'images', 'kelas_jasa', 'deskripsi_jasa', 'harga_paket_jasa', 'waktu_pengerjaan', 'maksimal_revisi', 'deskripsi_singkat'), [
            'kategori' => 'required|in:logo,banner,poster',
            'images' => 'required',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'kelas_jasa' => 'required|in:basic,standard,premium',
            'deskripsi_jasa' => 'required|max:500',
            'harga_paket_jasa' => 'required|integer',
            'waktu_pengerjaan' => 'required',
            'maksimal_revisi' => 'required|integer|max:20',
            'deskripsi_singkat' => 'required|max:300',
        ], [
            'kategori.required' => 'Kategori wajib di isi',
            'kategori.in' => 'Kategori harus berupa logo, banner, atau poster',
            'images.required' => 'Gambar jasa wajib di isi',
            'images.*.image' => 'Galeri Jasa harus berupa gambar',
            'images.*.required' => 'Galeri Jasa wajib di isi',
            'images.*.mimes' => 'Format Galeri Jasa tidak valid. Gunakan format jpeg, png, jpg',
            'images.*.max' => 'Ukuran Galeri Jasa tidak boleh lebih dari 5MB',
            'kelas_jasa.required' => 'Kelas Jasa wajib di isi',
            'kelas_jasa.in' => 'Kelas Jasa harus Basic, Standard, atau Premium',
            'deskripsi_jasa.required' => 'Deskripsi Jasa wajib di isi',
            'deskripsi_jasa.max' => 'Deskripsi Jasa maksimal 500 karakter',
            'harga_paket_jasa.required' => 'Harga Paket Jasa wajib di isi',
            'harga_paket_jasa.integer'=>'Harga Paket Jasa harus berupa angka',
            'waktu_pengerjaan.required' => 'Waktu Pengerjaan wajib di isi',
            'maksimal_revisi.required' => 'Maksimal Revisi wajib di isi',
            'maksimal_revisi.integer'=>'Maksimal Revisi harus berupa angka',
            'maksimal_revisi.max'=>'Maksimal Revisi maksimal 20',
            'deskripsi_singkat.required' => 'Deskripsi Singkat wajib di isi',
            'deskripsi_singkat.max' => 'Deskripsi Singkat maksimal 300 karakter',
        ]);
        
        if ($vJasa->fails()){
            $errors = [];
            foreach ($vJasa->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        // Cek jumlah gambar
        if (count($rt->file('images')) > 5) {
            return response()->json(['status' => 'error', 'message' => 'Maksimal 5 gambar untuk setiap jasa'], 400);
        }
        
        // Cek apakah kategori jasa sudah ada
        $existingJasa = Jasa::where('kategori', $rt->input('kategori'))->first();
        if ($existingJasa) {
            return response()->json(['status' => 'error', 'message' => 'Jasa dengan kategori ' . $rt->input('kategori') . ' sudah ada. Aplikasi ini hanya mendukung 3 jasa utama: Desain Logo, Desain Poster, dan Desain Banner.'], 400);
        }
        
        try {
            // Buat jasa baru
            $jasa = Jasa::create([
                'uuid' => Str::uuid(),
                'kategori' => $rt->input('kategori'),
                'deskripsi_jasa' => $rt->input('deskripsi_jasa')
            ]);
            
            // Buat paket jasa
            $paketJasa = PaketJasa::create([
                'kelas_jasa' => $rt->input('kelas_jasa'),
                'deskripsi_singkat' => $rt->input('deskripsi_singkat'),
                'harga_paket_jasa' => $rt->input('harga_paket_jasa'),
                'waktu_pengerjaan' => $rt->input('waktu_pengerjaan'),
                'maksimal_revisi' => $rt->input('maksimal_revisi'),
                'id_jasa' => $jasa->id_jasa
            ]);
            
            // Proses gambar
            if ($rt->hasFile('images')) {
                // Pastikan direktori ada
                $targetDir = $this->dirPath($jasa->kategori);
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                foreach ($rt->file('images') as $image) {
                    if ($image->isValid() && in_array($image->extension(), ['jpeg', 'png', 'jpg'])) {
                        $imageName = $image->hashName();
                        $image->move($targetDir, $imageName);
                        
                        JasaImage::create([
                            'image_path' => $imageName,
                            'id_jasa' => $jasa->id_jasa,
                        ]);
                    }
                }
            }
            
            return response()->json(['status' => 'success', 'message' => 'Jasa berhasil ditambahkan']);
        } catch (\Exception $e) {
            Log::error('Error creating jasa: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menambahkan jasa: ' . $e->getMessage()], 500);
        }
    }

    public function deleteJasa(Request $rt)
    {
        $validator = Validator::make($rt->only('id_jasa'), [
            'id_jasa' => 'required|string',
        ], [
            'id_jasa.required' => 'ID Jasa wajib di isi',
            'id_jasa.string' => 'ID Jasa harus berupa string',
        ]);
        
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        try {
            $jasa = Jasa::where('uuid', $rt->input('id_jasa'))->first();
            
            if (!$jasa) {
                return response()->json(['status' => 'error', 'message' => 'Jasa tidak ditemukan'], 404);
            }
            
            // Cek apakah jasa memiliki pesanan terkait
            $pesananCount = DB::table('pesanan')->where('id_jasa', $jasa->id_jasa)->count();
            if ($pesananCount > 0) {
                return response()->json(['status' => 'error', 'message' => 'Jasa tidak dapat dihapus karena memiliki pesanan terkait'], 400);
            }
            
            // Hapus gambar jasa
            $jasaImages = JasaImage::where('id_jasa', $jasa->id_jasa)->get();
            foreach ($jasaImages as $image) {
                $imagePath = $this->dirPath($jasa->kategori) . '/' . $image->image_path;
                if (file_exists($imagePath) && !is_dir($imagePath)) {
                    unlink($imagePath);
                }
                $image->delete();
            }
            
            // Hapus paket jasa
            PaketJasa::where('id_jasa', $jasa->id_jasa)->delete();
            
            // Hapus jasa
            $jasa->delete();
            
            return response()->json(['status' => 'success', 'message' => 'Jasa berhasil dihapus']);
        } catch (\Exception $e) {
            Log::error('Error deleting jasa: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus jasa: ' . $e->getMessage()], 500);
        }
    }
}