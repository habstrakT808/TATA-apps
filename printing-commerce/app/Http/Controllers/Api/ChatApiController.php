<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Admin;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class ChatApiController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase = null)
    {
        $this->firebase = $firebase;
    }

    /**
     * Mendapatkan daftar semua chat untuk admin
     */
    public function getChats()
    {
        try {
            $chats = Chat::with(['user', 'pesanan'])->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $chats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data chat'
            ], 500);
        }
    }

    /**
     * Mendapatkan detail chat beserta pesan-pesannya
     */
    public function getChatDetail($chatUuid)
    {
        try {
            $chat = Chat::with(['user', 'pesanan'])->where('uuid', $chatUuid)->first();

            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }

            // Ambil pesan-pesan dari chat ini
            $messages = ChatMessage::where('chat_uuid', $chatUuid)
                ->orderBy('created_at', 'asc')
                ->get();

            // Tandai semua pesan dari user sebagai telah dibaca
            ChatMessage::where('chat_uuid', $chatUuid)
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            // Reset unread count
            $chat->update(['unread_count' => 0]);

            return response()->json([
                'success' => true,
                'data' => [
                    'chat' => $chat,
                    'messages' => $messages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chat detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail chat'
            ], 500);
        }
    }

    /**
     * Kirim pesan baru dalam chat
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'required|exists:chats,uuid',
                'message' => 'required|string',
                'message_type' => 'sometimes|in:text,image,file',
                'file' => 'sometimes|file|max:5120' // max 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $chatUuid = $request->chat_uuid;
            $message = $request->message;
            $messageType = $request->message_type ?? 'text';
            $fileUrl = null;

            // Jika ada file yang diupload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/chat_files', $fileName);
                $fileUrl = asset('storage/chat_files/' . $fileName);
            }

            // Buat pesan baru
            $newMessage = ChatMessage::create([
                'uuid' => Str::uuid(),
                'chat_uuid' => $chatUuid,
                'sender_id' => auth()->id(),
                'sender_type' => 'admin',
                'message' => $message,
                'message_type' => $messageType,
                'file_url' => $fileUrl,
                'is_read' => false
            ]);

            // Update chat dengan pesan terakhir
            $chat = Chat::where('uuid', $chatUuid)->first();
            $chat->update([
                'last_message' => $message,
                'updated_at' => now()
            ]);

            // Jika ada Firebase Service, kirim notifikasi
            if ($this->firebase) {
                // Coba kirim notifikasi ke user
                $userId = $chat->user_id;
                $user = User::find($userId);

                if ($user && $user->fcm_token) {
                    $this->firebase->sendNotification(
                        $user->fcm_token,
                        'Pesan baru dari Admin',
                        $message,
                        [
                            'type' => 'chat',
                            'chat_uuid' => $chatUuid
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'data' => $newMessage
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan'
            ], 500);
        }
    }

    /**
     * Membuat chat baru dengan user
     */
    public function createChat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id_user',
                'pesanan_uuid' => 'sometimes|exists:pesanan,uuid',
                'initial_message' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $userId = $request->user_id;
            $pesananUuid = $request->pesanan_uuid;
            $initialMessage = $request->initial_message ?? 'Halo, admin TATA di sini. Ada yang bisa kami bantu?';

            // Cek apakah sudah ada chat untuk user dan pesanan ini
            $existingChat = Chat::where('user_id', $userId);
            if ($pesananUuid) {
                $existingChat = $existingChat->where('pesanan_uuid', $pesananUuid);
            }
            $existingChat = $existingChat->first();

            if ($existingChat) {
                return response()->json([
                    'success' => true,
                    'data' => $existingChat,
                    'message' => 'Chat sudah ada sebelumnya'
                ]);
            }

            // Buat chat baru
            $newChat = Chat::create([
                'uuid' => Str::uuid(),
                'user_id' => $userId,
                'admin_id' => auth()->id(),
                'pesanan_uuid' => $pesananUuid,
                'last_message' => $initialMessage,
                'unread_count' => 0
            ]);

            // Kirim pesan pembuka
            ChatMessage::create([
                'uuid' => Str::uuid(),
                'chat_uuid' => $newChat->uuid,
                'sender_id' => auth()->id(),
                'sender_type' => 'admin',
                'message' => $initialMessage,
                'message_type' => 'text',
                'is_read' => false
            ]);

            // Kirim notifikasi ke user
            if ($this->firebase) {
                $user = User::find($userId);
                if ($user && $user->fcm_token) {
                    $this->firebase->sendNotification(
                        $user->fcm_token,
                        'Percakapan baru dengan Admin TATA',
                        $initialMessage,
                        [
                            'type' => 'chat',
                            'chat_uuid' => $newChat->uuid
                        ]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'data' => $newChat
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat chat baru'
            ], 500);
        }
    }

    /**
     * Kirim notifikasi ke user
     */
    public function sendNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'required|exists:chats,uuid',
                'user_id' => 'required|exists:users,id_user',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            if (!$this->firebase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase service tidak tersedia'
                ], 500);
            }

            $user = User::find($request->user_id);
            
            if (!$user || !$user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki FCM token'
                ], 400);
            }

            $result = $this->firebase->sendNotification(
                $user->fcm_token,
                'Pesan baru dari Admin',
                $request->message,
                [
                    'type' => 'chat',
                    'chat_uuid' => $request->chat_uuid
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim notifikasi'
            ], 500);
        }
    }
}
