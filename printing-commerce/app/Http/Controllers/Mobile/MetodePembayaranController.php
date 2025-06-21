<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Revisi;
use App\Models\RevisiUser;
use App\Models\RevisiEditor;
use App\Models\User;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MetodePembayaranController extends Controller
{
    /**
     * Get all revisi for the authenticated user 
     */
    public function getAll(Request $request)
    {
        try {
            $idUser = User::where('id_auth', $request->user()->id_auth)->first()->id_user;

            $revisi = Revisi::join('pesanan', 'pesanan.id_pesanan', '=', 'revisi.id_pesanan')
                ->where('pesanan.id_user', $idUser)
                ->orderBy('revisi.created_at', 'desc')
                ->select(
                    'revisi.*',
                    'pesanan.uuid as uuid_pesanan'
                )
                ->get();

            // Modifikasi format data editor_file dan user_file jika berupa json/array
            $revisi = $revisi->map(function ($item) {
                return [
                    'id_revisi'     => $item->id_revisi,
                    'id_pesanan'    => $item->id_pesanan,
                    'uuid_pesanan'  => $item->uuid_pesanan,
                    'catatan'       => $item->catatan,
                    'status'        => $item->status,
                    'created_at'    => $item->created_at,
                    'updated_at'    => $item->updated_at,
                    'editor_file'   => json_decode($item->editor_file, true), // atau decode array jika perlu
                    'user_file'     => json_decode($item->user_file, true),
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Revisi berhasil diambil',
                'data' => $revisi
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil revisi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil revisi',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed revisi information
     */
    public function getDetail(Request $request, $uuid_pesanan)
    {
        try {
            // Ambil data revisi berdasarkan uuid_pesanan dari relasi pesanan
            $revisi = Revisi::with(['userFiles', 'editorFiles', 'pesanan'])
                ->whereHas('pesanan', function ($query) use ($uuid_pesanan) {
                    $query->where('uuid', $uuid_pesanan);
                })
                ->first();

            if (!$revisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Revisi tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Format respons agar editorFiles dan userFiles setara dengan data lainnya
            $data = [
                'id_revisi'     => $revisi->id_revisi,
                'id_pesanan'    => $revisi->id_pesanan,
                'uuid_pesanan'  => $revisi->pesanan->uuid,
                'catatan'       => $revisi->catatan,
                'status'        => $revisi->status,
                'created_at'    => $revisi->created_at,
                'updated_at'    => $revisi->updated_at,
                'image_hasil'    => $revisi->editorFiles->first()->nama_file,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Detail revisi berhasil diambil',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengambil detail revisi: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail revisi',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revision history for a pesanan
     */
    public function getRevisionHistory(Request $request, $id_pesanan){
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
            $pesanan = Pesanan::join('revisi', 'revisi.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('pesanan.uuid', $id_pesanan)
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Riwayat revisi berhasil diambil',
                'data' => [
                    'pesanan_info' => [
                        'uuid' => $pesanan->uuid,
                        'status' => $pesanan->status,
                        'maksimal_revisi' => $pesanan->maksimal_revisi,
                        'revisi_used' => $pesanan->revisi_used,
                        'revisi_tersisa' => $pesanan->revisi_tersisa
                    ],
                    'revisions' => $pesanan->revisions->map(function ($revision) {
                        return [
                            'uuid' => $revision->uuid,
                            'urutan_revisi' => $revision->urutan_revisi,
                            'status' => $revision->status,
                            'catatan_user' => $revision->catatan_user,
                            'catatan_editor' => $revision->catatan_editor,
                            'requested_at' => $revision->requested_at,
                            'started_at' => $revision->started_at,
                            'completed_at' => $revision->completed_at,
                            'user_files' => $revision->userFiles->map(function ($file) {
                                return [
                                    'nama_file' => $file->nama_file,
                                    'file_url' => url($file->file_path),
                                    'file_size' => $file->file_size,
                                    'uploaded_at' => $file->uploaded_at
                                ];
                            }),
                            'editor_files' => $revision->editorFiles->map(function ($file) {
                                return [
                                    'nama_file' => $file->nama_file,
                                    'file_url' => url($file->file_path),
                                    'file_size' => $file->file_size,
                                    'uploaded_at' => $file->uploaded_at
                                ];
                            })
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting revision history: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat revisi'
            ], 500);
        }
    }

    /**
     * Request revision for completed pesanan - Fixed Version
     */
    public function requestRevision(Request $request, $id_pesanan){
        try {
            $validator = Validator::make($request->only('catatan_revisi', 'file_revisi'), [
                'catatan_revisi' => 'required|string|max:500',
                'file_revisi' => 'sometimes|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240'
            ], [
                'catatan_revisi.required' => 'Catatan revisi wajib di isi',
                'catatan_revisi.string' => 'Catatan revisi harus berupa string',
                'catatan_revisi.max' => 'Catatan revisi maksimal 500 karakter',
                'file_revisi.file' => 'File harus berupa file',
                'file_revisi.mimes' => 'File harus berupa gambar (jpeg, png, jpg) atau dokumen (pdf, doc, docx)',
                'file_revisi.max' => 'File maksimal 10MB'
            ]);
            if ($validator->fails()){
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            $pesanan = Pesanan::where('uuid', $id_pesanan)
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            // Check if pesanan can be revised (must be completed first)
            if ($pesanan->status_pesanan !== 'menunggu_review') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan harus sudah selesai dikerjakan terlebih dahulu sebelum dapat direvisi'
                ], 422);
            }
            // Check revision limit - use dynamic count
            // Each record in 'revisi' table = 1 user revision request
            // So counting revisi records = counting how many times user requested revision
            $currentRevisionCount = $pesanan->revisions()->count();
            if ($currentRevisionCount >= $pesanan->maksimal_revisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Batas maksimal revisi ({$pesanan->maksimal_revisi}) sudah tercapai. Anda sudah melakukan {$currentRevisionCount} kali revisi."
                ], 400);
            }

            // Create new revision record - this represents 1 revision request from user
            $revisionNumber = $currentRevisionCount + 1;
            $revision = Revisi::create([
                'urutan_revisi' => $revisionNumber,
                'id_pesanan' => $pesanan->id_pesanan
            ]);

            // Handle file uploads for this specific revision
            if ($request->hasFile('file_revisi')) {
                $file = $request->file('file_revisi');
                $filename = $file->hashName();
                if (!Storage::disk('pesanan')->exists('revisi_user')) {
                    Storage::disk('pesanan')->makeDirectory('revisi_user');
                }
                Storage::disk('pesanan')->put('revisi_user/' . $filename, file_get_contents($file));

                // Store user files for this revision
                RevisiUser::create([
                    'nama_file' => $filename,
                    'catatan_user' => $request->catatan_revisi,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'id_user' => User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user,
                    'id_revisi' => $revision->id_revisi,
                ]);
            }
            // Update pesanan status to revision and clear editor assignment
            $pesanan->update([
                'status_pesanan' => 'revisi',
                'id_editor' => null,
                'assigned_at' => null
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Permintaan revisi berhasil dikirim',
                'data' => [
                    'revision' => $revision->load(['userFiles']),
                    'revisi_tersisa' => $pesanan->maksimal_revisi - $revisionNumber,
                    'urutan_revisi' => $revisionNumber,
                    'status_pesanan' => 'revisi',
                    'total_revisi_used' => $revisionNumber,
                    'maksimal_revisi' => $pesanan->maksimal_revisi
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error requesting revision: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal meminta revisi',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept final work (mark as completed)
     */
    public function acceptWork(Request $request, $id_pesanan){
        try {
            $pesanan = Pesanan::where('uuid', $id_pesanan)
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            if ($pesanan->status_pesanan !== 'menunggu_review') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan harus sudah selesai dikerjakan terlebih dahulu sebelum dapat diterima'
                ], 422);
            }
            $pesanan->update([
                'status_pesanan' => 'selesai',
                'completed_at' => Carbon::now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan telah diterima dan selesai',
            ]);
        } catch (\Exception $e) {
            Log::error('Error accepting work: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menerima pesanan',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download final files - Multiple approaches
     */
    public function downloadFiles(Request $request){
        try {
            $validator = Validator::make($request->only('id_pesanan', 'download_type'), [
                'id_pesanan' => 'required',
                'download_type' => 'nullable|in:binary,json,info'
            ], [
                'id_pesanan.required' => 'ID pesanan wajib di isi',
                'download_type.in' => 'Tipe download harus binary, json, atau info'
            ]);
            if ($validator->fails()){
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            $pesanan = Pesanan::where('uuid', $request->input('id_pesanan'))
                ->first();
                
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            // Check if pesanan allows download
            if (!in_array($pesanan->status_pesanan, ['menunggu_review', 'selesai'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File belum dapat didownload'
                ], 403);
            }
            // Find available file with fallback logic
            $fileInfo = $this->findAvailableFile($pesanan);
            if (!$fileInfo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada file hasil yang tersedia'
                ], 404);
            }
            $downloadType = $request->input('download_type', 'json');
            switch ($downloadType) {
                case 'binary':
                    return $this->downloadBinary($fileInfo);
                case 'info':
                    return $this->downloadInfo($fileInfo);
                case 'json':
                default:
                    return $this->downloadWithJson($fileInfo);
            }
        } catch (\Exception $e) {
            Log::error('Error downloading files: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil file',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find available file with fallback logic (loop sampai awal)
     */
    private function findAvailableFile($pesanan) {
        // Start from latest revision and work backwards
        $revisions = Revisi::where('id_pesanan', $pesanan->id_pesanan)
            ->orderBy('urutan_revisi', 'desc')
            ->get();
        foreach ($revisions as $revision) {
            $editorFiles = RevisiEditor::where('id_revisi', $revision->id_revisi)->get();
            foreach ($editorFiles as $editorFile) {
                $filePath = 'revisi_editor/' . $pesanan->uuid . '/' . $editorFile->nama_file;
                
                // Check if file exists in storage
                if (Storage::disk('pesanan')->exists($filePath)) {
                    return [
                        'file_path' => $filePath,
                        'nama_file' => $editorFile->nama_file,
                        'catatan_editor' => $editorFile->catatan_editor,
                        'urutan_revisi' => $revision->urutan_revisi,
                        'uploaded_at' => $editorFile->created_at,
                        'file_size' => Storage::disk('pesanan')->size($filePath)
                    ];
                }
            }
        }
        return null; // No file found after checking all revisions
    }

    /**
     * 1. Download file langsung sebagai binary
     */
    private function downloadBinary($fileInfo) {
        $filePath = $fileInfo['file_path'];
        $fileName = $fileInfo['nama_file'];
        
        // Get file content as binary
        $fileContent = Storage::disk('pesanan')->get($filePath);
        // Use application/octet-stream as default mime type
        $mimeType = 'application/octet-stream';
        
        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Length', strlen($fileContent))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * 2. Download info only (JSON saja)
     */
    private function downloadInfo($fileInfo) {
        return response()->json([
            'status' => 'success',
            'message' => 'Informasi file tersedia',
            'data' => [
                'nama_file' => $fileInfo['nama_file'],
                'catatan_editor' => $fileInfo['catatan_editor'],
                'urutan_revisi' => $fileInfo['urutan_revisi'],
                'uploaded_at' => $fileInfo['uploaded_at'],
                'file_size' => $fileInfo['file_size']
            ]
        ]);
    }

    /**
     * 3. Gabungan JSON + File (multipart response)
     */
    private function downloadWithJson($fileInfo) {
        $filePath = $fileInfo['file_path'];
        $fileName = $fileInfo['nama_file'];
        
        // Get file content as base64
        $fileContent = Storage::disk('pesanan')->get($filePath);
        $base64Content = base64_encode($fileContent);
        
        return response()->json([
            'status' => 'success',
            'message' => 'File dan informasi tersedia',
            'data' => [
                'file_info' => [
                    'nama_file' => $fileInfo['nama_file'],
                    'catatan_editor' => $fileInfo['catatan_editor'],
                    'urutan_revisi' => $fileInfo['urutan_revisi'],
                    'uploaded_at' => $fileInfo['uploaded_at'],
                    'file_size' => $fileInfo['file_size']
                ],
                'file_content' => [
                    'base64' => $base64Content,
                    'encoding' => 'base64'
                ]
            ]
        ]);
    }

    /**
     * Get all metode pembayaran
     */
    public function getAllMetodePembayaran()
    {
        try {
            $metodePembayaran = MetodePembayaran::where('is_active', true)
                ->orderBy('nama_metode', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data metode pembayaran berhasil diambil',
                'data' => $metodePembayaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting metode pembayaran: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data metode pembayaran'
            ], 500);
        }
    }

    /**
     * Get metode pembayaran by UUID
     */
    public function getMetodePembayaranDetail($uuid)
    {
        try {
            $metodePembayaran = MetodePembayaran::where('uuid', $uuid)
                ->where('is_active', true)
                ->first();

            if (!$metodePembayaran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Metode pembayaran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail metode pembayaran berhasil diambil',
                'data' => $metodePembayaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting metode pembayaran detail: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail metode pembayaran'
            ], 500);
        }
    }
}