<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\MetodePembayaran;
use App\Models\PaketJasa;
use Illuminate\Support\Str;
class MetodePembayaranController extends Controller
{
    private static $destinationPath;
    public function __construct(){
        if(env('APP_ENV', 'local') == 'local'){
            self::$destinationPath = public_path('assets3/img/metode_pembayaran');
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            self::$destinationPath = base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/metode_pembayaran';
        }
    }
    public function createMPembayaran(Request $rt){
        $v = Validator::make($rt->only('nama_metode_pembayaran', 'no_metode_pembayaran', 'deskripsi_1', 'deskripsi_2', 'thumbnail', 'icon'), [
            'nama_metode_pembayaran' => 'required|min:3|max:12',
            'no_metode_pembayaran' => 'required|min:3|max:20|regex:/^[0-9]+$/',
            'deskripsi_1' => 'required|max:500',
            'deskripsi_2' => 'required|max:500',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'icon' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'nama_metode_pembayaran.required' => 'Nama Metode Pembayaran wajib di isi',
            'nama_metode_pembayaran.min' => 'Nama Metode Pembayaran minimal 3 karakter',
            'nama_metode_pembayaran.max' => 'Nama Metode Pembayaran maksimal 12 karakter',
            'no_metode_pembayaran.required' => 'Nomor Metode Pembayaran wajib di isi',
            'no_metode_pembayaran.min' => 'Nomor Metode Pembayaran minimal 3 karakter',
            'no_metode_pembayaran.max' => 'Nomor Metode Pembayaran maksimal 20 karakter',
            'no_metode_pembayaran.regex' => 'Nomor Metode Pembayaran hanya boleh berisi angka',
            'deskripsi_1.required' => 'Deskripsi 1 Metode Pembayaran wajib di isi',
            'deskripsi_1.max' => 'Deskripsi 1 Metode Pembayaran maksimal 500 karakter',
            'deskripsi_2.required' => 'Deskripsi 2 Metode Pembayaran wajib di isi',
            'deskripsi_2.max' => 'Deskripsi 2 Metode Pembayaran maksimal 500 karakter',
            'thumbnail.required' => 'Thumbnail wajib di isi',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Format Thumbnail Metode Pembayaran tidak valid. Gunakan format jpeg, png, jpg',
            'thumbnail.max' => 'Ukuran Thumbnail Metode Pembayaran tidak boleh lebih dari 5MB',
            'icon.required' => 'Icon Metode Pembayaran wajib di isi',
            'icon.image' => 'Icon Metode Pembayaran harus berupa gambar',
            'icon.mimes' => 'Format Icon Metode Pembayaran tidak valid. Gunakan format jpeg, png, jpg',
            'icon.max' => 'Ukuran Icon Metode Pembayaran tidak boleh lebih dari 5MB',
        ]);
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        // Handle file uploads
        if($rt->hasFile('thumbnail')){
            $thumbnailFile = $rt->file('thumbnail');
            $thumbnailFilename = $thumbnailFile->hashName();
            $thumbnailFile->move(self::$destinationPath, $thumbnailFilename);
        }

        if($rt->hasFile('icon')){
            $iconFile = $rt->file('icon');
            $iconFilename = $iconFile->hashName();
            $iconFile->move(self::$destinationPath, $iconFilename);
        }

        $ins = MetodePembayaran::insert([
            'uuid' => Str::uuid(),
            'nama_metode_pembayaran' => $rt->input('nama_metode_pembayaran'),
            'no_metode_pembayaran' => $rt->input('no_metode_pembayaran'),
            'deskripsi_1' => $rt->input('deskripsi_1'),
            'deskripsi_2' => $rt->input('deskripsi_2'),
            'thumbnail' => $thumbnailFilename ?? null,
            'icon' => $iconFilename ?? null,
        ]);
        if(!$ins){
            return response()->json(['status'=>'error','message'=>'Gagal menambahkan data Metode Pembayaran'], 500);
        }
        return response()->json(['status'=>'success','message'=>'Data Metode Pembayaran berhasil ditambahkan']);
    }
    public function updateMPembayaran(Request $rt){
        $v = Validator::make($rt->only('id_metode_pembayaran', 'nama_metode_pembayaran', 'no_metode_pembayaran', 'deskripsi_1', 'deskripsi_2', 'thumbnail', 'icon'), [
            'id_metode_pembayaran' => 'required',
            'nama_metode_pembayaran' => 'required|min:3|max:12',
            'no_metode_pembayaran' => 'required|min:3|max:20|regex:/^[0-9]+$/',
            'deskripsi_1' => 'required|max:500',
            'deskripsi_2' => 'required|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'id_metode_pembayaran.required' => 'ID Metode Pembayaran wajib di isi',
            'nama_metode_pembayaran.required' => 'Nama Metode Pembayaran wajib di isi',
            'nama_metode_pembayaran.min' => 'Nama Metode Pembayaran minimal 3 karakter',
            'nama_metode_pembayaran.max' => 'Nama Metode Pembayaran maksimal 12 karakter',
            'no_metode_pembayaran.required' => 'Nomor Metode Pembayaran wajib di isi',
            'no_metode_pembayaran.min' => 'Nomor Metode Pembayaran minimal 3 karakter',
            'no_metode_pembayaran.max' => 'Nomor Metode Pembayaran maksimal 20 karakter',
            'no_metode_pembayaran.regex' => 'Nomor Metode Pembayaran hanya boleh berisi angka',
            'deskripsi_1.required' => 'Deskripsi 1 Metode Pembayaran wajib di isi',
            'deskripsi_1.max' => 'Deskripsi 1 Metode Pembayaran maksimal 500 karakter',
            'deskripsi_2.required' => 'Deskripsi 2 Metode Pembayaran wajib di isi',
            'deskripsi_2.max' => 'Deskripsi 2 Metode Pembayaran maksimal 500 karakter',
            'thumbnail.image' => 'Thumbnail harus berupa gambar',
            'thumbnail.mimes' => 'Format Thumbnail Metode Pembayaran tidak valid. Gunakan format jpeg, png, jpg',
            'thumbnail.max' => 'Ukuran Thumbnail Metode Pembayaran tidak boleh lebih dari 5MB',
            'icon.image' => 'Icon Metode Pembayaran harus berupa gambar',
            'icon.mimes' => 'Format Icon Metode Pembayaran tidak valid. Gunakan format jpeg, png, jpg',
            'icon.max' => 'Ukuran Icon Metode Pembayaran tidak boleh lebih dari 5MB',
        ]);
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        $metodePembayaran = MetodePembayaran::where('uuid', $rt->input('id_metode_pembayaran'))->first();
        if (!$metodePembayaran) {
            return response()->json(['status'=>'error','message'=>'Metode Pembayaran tidak ditemukan'], 404);
        }
        
        $thumbnailFilename = $metodePembayaran->thumbnail;
        $iconFilename = $metodePembayaran->icon;

        if($rt->hasFile('thumbnail')){
            $thumbnailFile = $rt->file('thumbnail');
            if(!($thumbnailFile->isValid() && in_array($thumbnailFile->extension(), ['jpeg', 'png', 'jpg']))){
                return response()->json(['status'=>'error','message'=>'Format Thumbnail tidak valid. Gunakan format jpeg, png, jpg'], 400);
            }
            
            // Delete old file if exists
            $oldThumbnailPath = self::$destinationPath . $metodePembayaran->thumbnail;
            if(file_exists($oldThumbnailPath) && !is_dir($oldThumbnailPath)){
                unlink($oldThumbnailPath);
            }
            
            $thumbnailFilename = $thumbnailFile->hashName();
            $thumbnailFile->move(self::$destinationPath, $thumbnailFilename);
        }

        if($rt->hasFile('icon')){
            $iconFile = $rt->file('icon');
            if(!($iconFile->isValid() && in_array($iconFile->extension(), ['jpeg', 'png', 'jpg']))){
                return response()->json(['status'=>'error','message'=>'Format Icon tidak valid. Gunakan format jpeg, png, jpg'], 400);
            }
            
            // Delete old file if exists
            $oldIconPath = self::$destinationPath . $metodePembayaran->icon;
            if(file_exists($oldIconPath) && !is_dir($oldIconPath)){
                unlink($oldIconPath);
            }
            
            $iconFilename = $iconFile->hashName();
            $iconFile->move(self::$destinationPath, $iconFilename);
        }

        $result = $metodePembayaran->update([
            'nama_metode_pembayaran' => $rt->input('nama_metode_pembayaran'),
            'no_metode_pembayaran' => $rt->input('no_metode_pembayaran'),
            'deskripsi_1' => $rt->input('deskripsi_1'),
            'deskripsi_2' => $rt->input('deskripsi_2'),
            'thumbnail' => $thumbnailFilename,
            'icon' => $iconFilename,
        ]);

        if (!$result){
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data Metode Pembayaran'], 500);
        }
        return response()->json(['status' =>'success','message'=>'Data Metode Pembayaran berhasil di perbarui']);
    }
    public function deleteMPembayaran(Request $rt){
        $v = Validator::make($rt->only('id_metode_pembayaran'), [
            'id_metode_pembayaran' => 'required',
        ], [
            'id_metode_pembayaran.required' => 'ID Metode Pembayaran wajib di isi',
        ]);
        if ($v->fails()){
            $errors = [];
            foreach ($v->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $metodePembayaran = MetodePembayaran::where('uuid',$rt->input('id_metode_pembayaran'))->firstOrFail();
        $ftd = self::$destinationPath . $metodePembayaran['thumbnail'];
        if (file_exists($ftd) && !is_dir($ftd)){
            unlink($ftd);
        }
        $ftd = self::$destinationPath . $metodePembayaran['icon'];
        if (file_exists($ftd) && !is_dir($ftd)){
            unlink($ftd);
        }
        if(!MetodePembayaran::where('uuid',$rt->input('id_metode_pembayaran'))->delete()){
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data Metode Pembayaran'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Data Metode Pembayaran berhasil dihapus']);
    }
}