<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\UtilityController;
use App\Models\Admin;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Pesanan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function showAll(Request $request){
        // Assign semua chat yang belum memiliki admin ke admin yang sedang login
        $this->assignAllChatsToAdmin($request);
        
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.chat', $dataShow);
    }
    
    public function showDetail(Request $request, $uuid){
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'chat' => Chat::with(['user', 'pesanan'])->where('uuid', $uuid)->first()
        ];
        return view('page.chat.detail', $dataShow);
    }
    
    public function getChats(Request $request)
    {
        try {
            // PERBAIKAN: Ambil admin berdasarkan session auth
            $authUser = $request->user();
            
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            Log::info("Auth user data: " . json_encode($authUser));
            
            // Cari admin berdasarkan id_auth dari session
            $admin = Admin::where('id_auth', $authUser['id_auth'])->first();
            
            if (!$admin) {
                Log::error("Admin not found for id_auth: " . $authUser['id_auth']);
                return response()->json([
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan'
                ], 404);
            }
            
            Log::info("Admin {$admin->nama_admin} (ID: {$admin->id_admin}) requesting chats");
            
            // Ubah query untuk menampilkan semua chat yang ada di database
            // Tidak lagi memfilter berdasarkan admin_id
            $chats = Chat::with(['user', 'pesanan'])
                ->orderBy('updated_at', 'desc')
                ->get();
                
            Log::info("Found " . $chats->count() . " chats total");
            
            // Transform data untuk frontend
            $transformedChats = $chats->map(function($chat) {
                return [
                    'uuid' => $chat->uuid,
                    'user' => [
                        'id_user' => $chat->user_id,
                        'nama_user' => $chat->user->nama_user ?? 'Unknown User',
                        'profile_picture' => $chat->user->profile_picture ?? null,
                    ],
                    'pesanan' => $chat->pesanan ? [
                        'id_pesanan' => $chat->pesanan->id_pesanan,
                        'uuid' => $chat->pesanan->uuid,
                    ] : null,
                    'last_message' => $chat->last_message,
                    'updated_at' => $chat->updated_at,
                    'unread_count' => $chat->unread_count ?? 0,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $transformedChats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting chats: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar chat: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getMessages(Request $request)
    {
        try {
            $chatUuid = $request->input('chat_uuid');
            
            if (!$chatUuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat UUID tidak ditemukan'
                ], 400);
            }
            
            $messages = ChatMessage::where('chat_uuid', $chatUuid)
                ->orderBy('created_at', 'asc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'chat_uuid' => 'required|string',
                'message' => 'required_without:file_url|string|nullable',
                'message_type' => 'required|in:text,image,file',
                'file_url' => 'nullable|string',
            ]);
            
            // PERBAIKAN: Ambil admin dari session yang benar
            $authUser = $request->user();
            
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            // Cari admin berdasarkan id_auth dari session
            $admin = Admin::where('id_auth', $authUser['id_auth'])->first();
            
            if (!$admin) {
                Log::error("Admin not found for id_auth: " . $authUser['id_auth']);
                return response()->json([
                    'success' => false,
                    'message' => 'Data admin tidak ditemukan'
                ], 404);
            }
            
            $chat = Chat::where('uuid', $request->chat_uuid)->first();
            
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }
            
            // Selalu tetapkan admin yang sedang login sebagai admin chat
            // Ini memastikan chat akan muncul di daftar chat admin
            $chat->admin_id = $admin->id_admin;
            $chat->save();
            
            // Create message - PERBAIKAN: gunakan id_auth untuk sender_id
            $message = new ChatMessage();
            $message->uuid = Str::uuid();
            $message->chat_uuid = $request->chat_uuid;
            $message->sender_id = $authUser['id_auth']; // ← PERBAIKAN: gunakan id_auth
            $message->sender_type = 'admin';
            $message->message = $request->message;
            $message->message_type = $request->message_type;
            $message->file_url = $request->file_url;
            $message->is_read = false;
            $message->save();
            
            // Update chat last message
            $chat->last_message = substr($request->message, 0, 50);
            $chat->updated_at = now();
            $chat->save();
            
            Log::info("Message sent by admin {$admin->nama_admin} to chat {$chat->uuid}");
            
            return response()->json([
                'success' => true,
                'data' => $message
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function markAsRead(Request $request)
    {
        try {
            $request->validate([
                'chat_uuid' => 'required|string',
            ]);
            
            $messages = ChatMessage::where('chat_uuid', $request->chat_uuid)
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->update(['is_read' => true]);
                
            return response()->json([
                'success' => true,
                'message' => 'Pesan telah ditandai sebagai telah dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai pesan sebagai telah dibaca: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:10240', // Max 10MB
            ]);
            
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            
            $path = $file->storeAs('chat_files', $fileName, 'public');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_url' => asset('storage/' . $path),
                    'file_name' => $fileName,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function assignChatToAdmin(Request $request)
    {
        try {
            $request->validate([
                'chat_uuid' => 'required|string',
                'admin_id' => 'required|integer',
            ]);
            
            $chat = Chat::where('uuid', $request->chat_uuid)->first();
            
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }
            
            $chat->admin_id = $request->admin_id;
            $chat->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Chat berhasil ditugaskan ke admin'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan chat ke admin: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menetapkan admin yang sedang login sebagai admin untuk semua chat yang belum memiliki admin
     */
    private function assignAllChatsToAdmin(Request $request) {
        try {
            $authUser = $request->user();
            if (!$authUser) {
                return;
            }
            
            // Cari admin berdasarkan id_auth dari session
            $admin = Admin::where('id_auth', $authUser['id_auth'])->first();
            if (!$admin) {
                return;
            }
            
            // Ambil semua chat yang belum memiliki admin
            $unassignedChats = Chat::whereNull('admin_id')->get();
            
            if ($unassignedChats->count() > 0) {
                Log::info("Assigning {$unassignedChats->count()} chats to admin {$admin->nama_admin}");
                
                foreach ($unassignedChats as $chat) {
                    $chat->admin_id = $admin->id_admin;
                    $chat->save();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error assigning chats to admin: ' . $e->getMessage());
        }
    }
}