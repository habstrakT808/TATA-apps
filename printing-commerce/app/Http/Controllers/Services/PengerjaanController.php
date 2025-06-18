<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\ChatMessage;
use App\Models\Editor;
use App\Models\Revisi;
use App\Models\RevisiEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
class PengerjaanController extends Controller
{
    private function dirPath($idPesanan){
        if(env('APP_ENV', 'local') == 'local'){
            return public_path('assets3/img/pesanan/' . $idPesanan);
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            return base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/pesanan/' . $idPesanan;
        }
    }
    /**
     * ADMIN: Assign editor to handle order or revision
     */
    public function assignEditor(Request $request){
        try {
            $validator = Validator::make($request->only('id_pesanan', 'status_pesanan', 'id_editor'), [
                'id_pesanan' => 'required',
                'status_pesanan' => 'required|in:dikerjakan,revisi',
                'id_editor' => 'required',
            ], [
                'id_pesanan.required' => 'ID pesanan wajib diisi',
                'status_pesanan.required' => 'Status pesanan wajib diisi',
                'status_pesanan.in' => 'Status pesanan harus diantara dikerjakan, revisi',
                'id_editor.required' => 'ID editor wajib diisi',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            $pesanan = Pesanan::where('uuid', $request->input('id_pesanan'))->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            // Validate pesanan status
            if (!in_array($pesanan->status_pesanan, ['menunggu_editor', 'revisi'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan harus dalam status menunggu editor atau revisi untuk assign editor'
                ], 400);
            }
            $editor = Editor::where('id_editor', $request->input('id_editor'))->first();
            if(!$editor){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Editor tidak ditemukan'
                ], 404);
            }
            // Check if editor is already assigned and working
            if ($pesanan->id_editor && in_array($pesanan->status_pesanan, ['dikerjakan', 'revisi'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Editor sudah sedang mengerjakan pesanan ini'
                ], 400);
            }
            // Update pesanan with assigned editor
            switch($request->input('status_pesanan')){
                case 'dikerjakan':
                    if($pesanan->status_pesanan == 'dikerjakan'){
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Pesanan sudah dalam status dikerjakan'
                        ], 400);
                    }
                    // If id_editor is provided, assign the editor
                    if (!$request->has('id_editor') || is_null($request->input('id_editor'))) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Silahkan pilih editor terlebih dahulu'
                        ], 400);
                    }
                    $editor = Editor::where('id_editor', $request->input('id_editor'))->first();
                    if (!$editor) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Editor tidak ditemukan'
                        ], 404);
                    }
                    $updateData['id_editor'] = $request->input('id_editor');
                    $updateData['assigned_at'] = Carbon::now();
                    $updateData['status_pesanan'] = 'dikerjakan';
                    break;
                case 'revisi':
                    // Editor tidak bisa mengerjakan revisi jika belum pernah revisi (revisi count = 0)
                    if ($pesanan->revisions()->count() == 0) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Tidak bisa revisi karena pesanan belum pernah direvisi'
                        ], 400);
                    }
                    // When assigning editor to revision, keep status as 'revisi'
                    if (!$request->has('id_editor') || is_null($request->input('id_editor'))) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Silahkan pilih editor terlebih dahulu'
                        ], 400);
                    }
                    $editor = Editor::where('id_editor', $request->input('id_editor'))->first();
                    if (!$editor) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Editor tidak ditemukan'
                        ], 404);
                    }
                    $updateData['id_editor'] = $request->input('id_editor');
                    $updateData['assigned_at'] = Carbon::now();
                    $updateData['status_pesanan'] = 'revisi';
                    break;
            }
            $pesanan->update($updateData);
            // //send message to chat
            // app(ChatController::class)->assignEditor(auth()->id(), $pesanan, $editor); 
            return response()->json([
                'status' => 'success',
                'message' => 'Editor berhasil ditugaskan untuk ' . ($request->input('status_pesanan') == 'revisi' ? 'revisi' : 'pengerjaan'),
                'data' => [
                    'pesanan' => $pesanan->fresh(),
                    'assigned_editor' => $editor,
                    'revision_number' => $pesanan->revisions()->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menugaskan editor: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * ADMIN: Mark revision as completed either first time or revision
     */
    public function markRevisionCompleted(Request $request){
        try {
            $validator = Validator::make($request->only('id_pesanan', 'file_hasil', 'catatan_editor'), [
                'id_pesanan' => 'required',
                'file_hasil' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,webp|max:2048',
                'catatan_editor' => 'nullable|string|max:500',
            ], [
                'id_pesanan.required' => 'ID pesanan wajib diisi',
                'file_hasil.required' => 'File revisi wajib diisi',
                'file_hasil.file' => 'File revisi harus berupa file',
                'file_hasil.mimes' => 'File revisi harus berupa file PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, atau WEBP',
                'file_hasil.max' => 'File revisi maksimal 2MB',
                'catatan_editor.string' => 'Catatan harus berupa teks',
                'catatan_editor.max' => 'Catatan maksimal 500 karakter'
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            $pesanan = Pesanan::where('uuid', $request->input('id_pesanan'))->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            if($pesanan->status_pesanan == 'menunggu_review'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan sedang menunggu review dari user'
                ], 400);
            }
            if(!in_array($pesanan->status_pesanan, ['dikerjakan', 'revisi'])){
                return response()->json([   
                    'status' => 'error',
                    'message' => 'Pesanan tidak dalam status dikerjakan atau revisi'
                ], 400);
            }
            if(!file_exists($this->dirPath($pesanan->uuid) . '/revisi_editor')){
                mkdir($this->dirPath($pesanan->uuid) . '/revisi_editor');
            }
            if($pesanan->revisions()->count() == 0){
                if(!file_exists($this->dirPath($pesanan->uuid) . '/revisi_editor')){
                    mkdir($this->dirPath($pesanan->uuid) . '/revisi_editor');
                }
                $file_name = $pesanan->uuid . '_revisi_' . $pesanan->revisions()->count() . '.' . $request->file('file_hasil')->getClientOriginalExtension();
                file_put_contents($this->dirPath($pesanan->uuid) . '/revisi_editor/' . $file_name, file_get_contents($request->file('file_hasil')));
                $idRevisi = Revisi::insertGetId([
                    'urutan_revisi' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'id_pesanan' => $pesanan->id_pesanan,
                ]);
            }
            else{
                $file_name = $pesanan->uuid . '_revisi_' . $pesanan->revisions()->count() . '.' . $request->file('file_hasil')->getClientOriginalExtension();
                file_put_contents($this->dirPath($pesanan->uuid) . '/revisi_editor/' . $file_name, file_get_contents($request->file('file_hasil')));
                $idRevisi = Revisi::where('id_pesanan', $pesanan->id_pesanan)->where('urutan_revisi', $pesanan->revisions()->count())->first()->id_revisi;
                Revisi::where('id_pesanan', $pesanan->id_pesanan)->where('urutan_revisi', $pesanan->revisions()->count())->update([
                    'updated_at' => Carbon::now(),
                ]);
            }
            // Insert assignment history to revisi_editor table
            RevisiEditor::create([
                'nama_file' => $file_name,
                'catatan_editor' => $request->input('catatan_editor') ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'id_editor' => $pesanan->id_editor,
                'id_revisi' => $idRevisi,
            ]);
            // Update pesanan status
            $message = $pesanan->status_pesanan == 'dikerjakan' ? 'dikerjakan dan sekarang menunggu review user' : 'direvisi dan sekarang menunggu review user';
            $pesanan->update([
                'status_pesanan' => 'menunggu_review',
            ]);
            //send message to chat
            // app(ChatController::class)->sendMessage(auth()->id(), $pesanan, $request->input('catatan_editor') ?? null);
            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil selesai ' . $message,
                'data' => $pesanan->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai revisi selesai: ' . $e->getMessage()
            ], 500);
        }
    }
}