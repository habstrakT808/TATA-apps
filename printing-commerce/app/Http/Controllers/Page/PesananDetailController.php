<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Editor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PesananDetailController extends Controller
{
    /**
     * Update data pesanan
     */
    public function updatePesanan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_pesanan' => 'required|string',
                'status' => 'nullable|string',
                'editor_id' => 'nullable|exists:editor,id_editor',
                'status_pengerjaan' => 'nullable|in:menunggu,diproses,dikerjakan,selesai',
                'estimasi_mulai' => 'nullable|date',
                'estimasi_selesai' => 'nullable|date',
                'maksimal_revisi' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid: ' . $validator->errors()->first()
                ], 422);
            }

            $pesanan = Pesanan::where('uuid', $request->id_pesanan)->first();
            
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Update status pesanan jika ada
            if ($request->has('status') && !empty($request->status)) {
                $pesanan->status_pesanan = $request->status;
            }

            // Update editor jika ada
            if ($request->has('editor_id') && !empty($request->editor_id)) {
                $pesanan->id_editor = $request->editor_id;
            }
            
            // Update status pengerjaan jika ada
            if ($request->has('status_pengerjaan') && !empty($request->status_pengerjaan)) {
                $pesanan->status_pengerjaan = $request->status_pengerjaan;
            }
            
            // Update estimasi waktu jika ada
            if ($request->has('estimasi_mulai') && !empty($request->estimasi_mulai)) {
                $pesanan->estimasi_mulai = $request->estimasi_mulai;
            }
            
            if ($request->has('estimasi_selesai') && !empty($request->estimasi_selesai)) {
                $pesanan->estimasi_selesai = $request->estimasi_selesai;
            }
            
            // Update maksimal revisi jika ada
            if ($request->has('maksimal_revisi') && $request->maksimal_revisi !== null) {
                $pesanan->maksimal_revisi = $request->maksimal_revisi;
            }

            $pesanan->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pesanan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating pesanan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file hasil desain
     */
    public function uploadHasilDesain(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_pesanan' => 'required|string',
                'gambar_hasil_desain' => 'required|file|image|max:5120' // max 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid: ' . $validator->errors()->first()
                ], 422);
            }

            $pesanan = Pesanan::where('uuid', $request->id_pesanan)->first();
            
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Upload file
            if ($request->hasFile('gambar_hasil_desain')) {
                $file = $request->file('gambar_hasil_desain');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Buat direktori jika belum ada
                $path = 'assets3/img/pesanan/' . $pesanan->uuid . '/hasil_desain';
                if (!file_exists(public_path($path))) {
                    mkdir(public_path($path), 0777, true);
                }
                
                // Pindahkan file
                $file->move(public_path($path), $fileName);
                
                // Simpan nama file ke database
                $pesanan->file_hasil_desain = $fileName;
                $pesanan->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'File berhasil diupload',
                    'filename' => $fileName,
                    'file_path' => asset($path . '/' . $fileName)
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error uploading hasil desain: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of editors
     */
    public function getEditors()
    {
        try {
            $editors = Editor::select('id_editor', 'nama_editor')->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $editors
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting editors: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
