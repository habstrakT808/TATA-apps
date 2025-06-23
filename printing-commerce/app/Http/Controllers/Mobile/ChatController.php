<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Admin;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Models\Auth as AuthModel;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\Transaksi;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Create a new chat room or return existing one
     */
    public function createOrGetChatRoom(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pesanan_uuid' => 'nullable|string|exists:pesanan,uuid',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            // If pesanan_uuid is provided, check if chat already exists for this order
            if ($request->pesanan_uuid) {
                $existingChat = Chat::where('pesanan_uuid', $request->pesanan_uuid)
                               ->where('user_id', $user->id_user)
                               ->first();
                
                if ($existingChat) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Chat room already exists',
                        'data' => $existingChat
                    ]);
                }
                
                // Get pesanan details
                $pesanan = Pesanan::where('uuid', $request->pesanan_uuid)->first();
                
                if (!$pesanan) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Pesanan tidak ditemukan'
                    ], 404);
                }
                
                // Find available admin (admin_chat role)
                $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                        ->where('auth.role', 'admin_chat')
                        ->inRandomOrder()
                        ->first();
                
                if (!$admin) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tidak ada admin chat yang tersedia'
                    ], 500);
                }
                
                // Create new chat room
                $chat = new Chat();
                $chat->uuid = Str::uuid();
                $chat->user_id = $user->id_user;
                $chat->admin_id = $admin->id_admin;
                $chat->pesanan_uuid = $request->pesanan_uuid;
                $chat->last_message = 'Chat dibuat';
                $chat->unread_count = 0;
                $chat->save();
                
                // Create welcome message
                $welcomeMessage = new ChatMessage();
                $welcomeMessage->uuid = Str::uuid();
                $welcomeMessage->chat_uuid = $chat->uuid;
                $welcomeMessage->sender_id = $admin->id_admin;
                $welcomeMessage->sender_type = 'admin';
                $welcomeMessage->message = "Halo! Admin TATA siap membantu Anda terkait pesanan #{$pesanan->uuid}";
                $welcomeMessage->message_type = 'text';
                $welcomeMessage->is_read = false;
                $welcomeMessage->save();
                
                // Update chat's last message
                $chat->last_message = $welcomeMessage->message;
                $chat->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Chat room created successfully',
                    'data' => $chat
                ], 201);
            }
            
            // If no pesanan_uuid, create a general chat
            $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                    ->where('auth.role', 'admin_chat')
                    ->inRandomOrder()
                    ->first();
            
            if (!$admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada admin chat yang tersedia'
                ], 500);
            }
            
            // Create new chat room
            $chat = new Chat();
            $chat->uuid = Str::uuid();
            $chat->user_id = $user->id_user;
            $chat->admin_id = $admin->id_admin;
            $chat->last_message = 'Chat dibuat';
            $chat->unread_count = 0;
            $chat->save();
            
            // Create welcome message
            $welcomeMessage = new ChatMessage();
            $welcomeMessage->uuid = Str::uuid();
            $welcomeMessage->chat_uuid = $chat->uuid;
            $welcomeMessage->sender_id = $admin->id_admin;
            $welcomeMessage->sender_type = 'admin';
            $welcomeMessage->message = "Halo! Ada yang bisa kami bantu?";
            $welcomeMessage->message_type = 'text';
            $welcomeMessage->is_read = false;
            $welcomeMessage->save();
            
            // Update chat's last message
            $chat->last_message = $welcomeMessage->message;
            $chat->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Chat room created successfully',
                'data' => $chat
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create chat room: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'required|string|exists:chats,uuid',
                'message' => 'required_without:file_url|string|nullable',
                'message_type' => 'required|in:text,image,file',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $chat = Chat::where('uuid', $request->chat_uuid)
                   ->where('user_id', $user->id_user)
                   ->first();
            
            if (!$chat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }

            // Create message
            $chatMessage = new ChatMessage();
            $chatMessage->uuid = Str::uuid();
            $chatMessage->chat_uuid = $chat->uuid;
            $chatMessage->sender_id = $userId;
            $chatMessage->sender_type = 'user';
            $chatMessage->message = $request->message;
            $chatMessage->message_type = $request->message_type;
            $chatMessage->file_url = $request->file_url;
            $chatMessage->save();
            
            // Update last message
            $chat->last_message = substr($request->message, 0, 50);
            $chat->unread_count = ($chat->unread_count ?? 0) + 1;
            $chat->updated_at = now();
            $chat->save();
            
            Log::info("Message sent to chat {$chat->uuid} by user {$userId}");
            Log::info("Chat admin_id: {$chat->admin_id}");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim',
                'data' => $chatMessage
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get messages for a chat
     */
    public function getMessages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'required|string|exists:chats,uuid',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:10|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $chat = Chat::where('uuid', $request->chat_uuid)
                   ->where('user_id', $user->id_user)
                   ->first();
            
            if (!$chat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }

            $page = $request->page ?? 1;
            $limit = $request->limit ?? 20;
            
            // PERBAIKAN: Get messages dengan sender info
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                       ->orderBy('created_at', 'desc')
                       ->paginate($limit, ['*'], 'page', $page);
            
            // Transform messages untuk include sender info
            $transformedMessages = $messages->getCollection()->map(function($message) {
                $senderInfo = null;
                
                if ($message->sender_type === 'user') {
                    $user = User::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $user ? [
                        'id' => $user->id_user,
                        'name' => $user->nama_user,
                        'type' => 'user'
                    ] : null;
                } elseif ($message->sender_type === 'admin') {
                    $admin = Admin::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $admin ? [
                        'id' => $admin->id_admin,
                        'name' => $admin->nama_admin,
                        'type' => 'admin'
                    ] : null;
                }
                
                return [
                    'id' => $message->id,
                    'uuid' => $message->uuid,
                    'chat_uuid' => $message->chat_uuid,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'sender_info' => $senderInfo,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'file_url' => $message->file_url,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ];
            });
            
            // Update pagination data
            $messages->setCollection($transformedMessages);
            
            // Mark messages as read if sent by admin
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            Log::info("Returned " . $transformedMessages->count() . " messages for chat {$chat->uuid}");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan pesan',
                'data' => $messages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Upload file for chat
     */
    public function uploadFile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // max 10MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }
            
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Pastikan direktori ada
            $uploadPath = storage_path('app/public/chat_files');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $path = $file->storeAs('chat_files', $fileName, 'public');
            
            // ✅ SELALU GUNAKAN PROXY UNTUK SEMUA REQUEST
            $url = url('image-proxy.php?type=chat&file=' . $fileName);
            
            Log::info("File uploaded successfully", [
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $fileName,
                'path' => $path,
                'url' => $url,
                'file_exists' => file_exists(storage_path('app/public/' . $path)),
                'full_path' => storage_path('app/public/' . $path)
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diunggah',
                'data' => [
                    'file_url' => $url,
                    'file_name' => $fileName
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Upload file error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunggah file: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get chat list for a user
     */
    public function getChatList(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            $chats = Chat::with(['admin', 'pesanan'])
                    ->where('user_id', $user->id_user)
                    ->orderBy('updated_at', 'desc')
                    ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan daftar chat',
                'data' => $chats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan daftar chat: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get messages by pesanan UUID - untuk kompatibilitas dengan aplikasi Flutter
     */
    public function getMessagesByPesanan($pesananUuid)
    {
        try {
            Log::info("=== GET MESSAGES BY PESANAN START ===");
            Log::info("Pesanan UUID: $pesananUuid");
            
            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                Log::error("User not found for auth_id: $userId");
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            Log::info("User found: {$user->nama_user} (ID: {$user->id_user})");
            
            // Check if this is a short order ID instead of UUID
            if (!Str::contains($pesananUuid, '-') && strlen($pesananUuid) <= 8) {
                // Try to find the corresponding UUID from the Transaksi table
                $transaksi = Transaksi::where('order_id', $pesananUuid)
                    ->whereHas('toPesanan', function($query) use ($user) {
                        $query->where('id_user', $user->id_user);
                    })
                    ->first();
                
                if ($transaksi) {
                    $pesananUuid = $transaksi->toPesanan->uuid;
                    Log::info("Found UUID $pesananUuid for order ID $pesananUuid");
                } else {
                    Log::warning("Could not find UUID for order ID $pesananUuid");
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Chat untuk pesanan ini tidak ditemukan'
                    ], 404);
                }
            }
            
            // Find chat for this pesanan
            $chat = Chat::where('pesanan_uuid', $pesananUuid)
                   ->where('user_id', $user->id_user)
                   ->first();
            
            if (!$chat) {
                Log::warning("Chat not found for pesanan: $pesananUuid, user: {$user->id_user}");
                
                // If chat doesn't exist, try to create one
                try {
                    $chat = $this->createChatForOrder($pesananUuid, $user->id_user);
                    
                    if (!$chat) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Chat untuk pesanan ini tidak ditemukan'
                        ], 404);
                    }
                } catch (\Exception $e) {
                    Log::error('Error creating chat: ' . $e->getMessage());
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Chat untuk pesanan ini tidak ditemukan'
                    ], 404);
                }
            }
            
            Log::info("Chat found: {$chat->uuid}");
            
            // Get messages
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                       ->orderBy('created_at', 'asc')
                       ->get();
            
            Log::info("Found " . $messages->count() . " messages");
            
            // Transform messages untuk include sender info
            $transformedMessages = $messages->map(function($message) {
                $senderInfo = null;
                
                if ($message->sender_type === 'user') {
                    $user = User::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $user ? [
                        'id' => $user->id_user,
                        'name' => $user->nama_user,
                        'type' => 'user'
                    ] : null;
                } elseif ($message->sender_type === 'admin') {
                    $admin = Admin::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $admin ? [
                        'id' => $admin->id_admin,
                        'name' => $admin->nama_admin,
                        'type' => 'admin'
                    ] : null;
                }
                
                Log::info("Message ID {$message->id}: sender_type={$message->sender_type}, sender_id={$message->sender_id}, message={$message->message}");
                if ($senderInfo) {
                    Log::info("Sender info: " . json_encode($senderInfo));
                }
                
                return [
                    'id' => $message->id,
                    'uuid' => $message->uuid,
                    'chat_uuid' => $message->chat_uuid,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'sender_info' => $senderInfo,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'file_url' => $message->file_url,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ];
            });
            
            // Mark messages as read if sent by admin
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            Log::info("=== GET MESSAGES BY PESANAN END ===");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan pesan',
                'messages' => $transformedMessages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages by pesanan: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get messages by Order ID - untuk kompatibilitas dengan Flutter app
     * Route: GET /api/mobile/chat/messages/{orderId}
     */
    public function getMessagesByOrderId($orderId)
    {
        try {
            Log::info("=== GET MESSAGES BY ORDER ID START ===");
            Log::info("Order ID: $orderId");
            
            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                Log::error("User not found for auth_id: $userId");
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            Log::info("User found: {$user->nama_user} (ID: {$user->id_user})");
            
            // ✅ PERBAIKAN: Cek apakah ini adalah chat UUID langsung (untuk direct chat)
            $chat = Chat::where('uuid', $orderId)
                   ->where('user_id', $user->id_user)
                   ->first();
            
            if ($chat) {
                Log::info("Found direct chat with UUID: $orderId");
                
                // Get messages untuk direct chat
                $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                           ->orderBy('created_at', 'asc')
                           ->get();
                
                Log::info("Found " . $messages->count() . " messages for direct chat");
                
                // Transform messages
                $transformedMessages = $messages->map(function($message) {
                    $senderInfo = null;
                    
                    if ($message->sender_type === 'user') {
                        $user = User::where('id_auth', $message->sender_id)->first();
                        $senderInfo = $user ? [
                            'id' => $user->id_user,
                            'name' => $user->nama_user,
                            'type' => 'user'
                        ] : null;
                    } elseif ($message->sender_type === 'admin') {
                        $admin = Admin::where('id_auth', $message->sender_id)->first();
                        $senderInfo = $admin ? [
                            'id' => $admin->id_admin,
                            'name' => $admin->nama_admin,
                            'type' => 'admin'
                        ] : null;
                    }
                    
                    return [
                        'id' => $message->id,
                        'uuid' => $message->uuid,
                        'chat_uuid' => $message->chat_uuid,
                        'sender_id' => $message->sender_id,
                        'sender_type' => $message->sender_type,
                        'sender_info' => $senderInfo,
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'file_url' => $message->file_url,
                        'is_read' => $message->is_read,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                    ];
                });
                
                // Mark admin messages as read
                ChatMessage::where('chat_uuid', $chat->uuid)
                    ->where('sender_type', 'admin')
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
                
                Log::info("=== GET MESSAGES BY ORDER ID END (DIRECT CHAT) ===");
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Berhasil mendapatkan pesan',
                    'messages' => $transformedMessages,
                    'chat_info' => [
                        'chat_uuid' => $chat->uuid,
                        'chat_type' => 'direct',
                        'order_id' => $orderId
                    ]
                ]);
            }
            
            // Jika bukan direct chat, lanjutkan dengan logika pencarian pesanan
            $pesananUuid = null;
            
            // 1. Coba langsung sebagai UUID pesanan
            $pesanan = Pesanan::where('uuid', $orderId)->first();
            if ($pesanan && $pesanan->id_user == $user->id_user) {
                $pesananUuid = $orderId;
                Log::info("Found pesanan directly with UUID: $orderId");
            }
            
            // 2. Coba cari di tabel transaksi berdasarkan order_id
            if (!$pesananUuid) {
                $transaksi = Transaksi::where('order_id', $orderId)->first();
                if ($transaksi) {
                    $pesanan = Pesanan::where('uuid', $transaksi->pesanan_uuid)
                        ->where('id_user', $user->id_user)
                        ->first();
                    if ($pesanan) {
                        $pesananUuid = $pesanan->uuid;
                        Log::info("Found pesanan from transaksi: $pesananUuid");
                    }
                }
            }
            
            // 3. Jika tidak ketemu, return pesan sistem
            if (!$pesananUuid) {
                Log::warning("Could not find any valid pesanan for order: $orderId");
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Chat untuk pesanan ini belum tersedia',
                    'messages' => [
                        [
                            'id' => 0,
                            'uuid' => 'system-' . time(),
                            'chat_uuid' => 'system',
                            'sender_id' => 0,
                            'sender_type' => 'system',
                            'sender_info' => [
                                'id' => 0,
                                'name' => 'System',
                                'type' => 'system'
                            ],
                            'message' => "Pesanan dengan ID '$orderId' tidak ditemukan atau belum memiliki chat. Silakan periksa kembali ID pesanan Anda.",
                            'message_type' => 'text',
                            'file_url' => null,
                            'is_read' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ]
                ]);
            }
            
            // Lanjutkan dengan logika pesanan normal...
            $chat = Chat::where('pesanan_uuid', $pesananUuid)
                ->where('user_id', $user->id_user)
                ->first();
            
            if (!$chat) {
                Log::info("Creating new chat for pesanan: $pesananUuid");
                try {
                    $chat = $this->createChatForOrder($pesananUuid, $user->id_user);
                } catch (\Exception $e) {
                    Log::error('Error creating chat: ' . $e->getMessage());
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal membuat chat untuk pesanan ini'
                    ], 500);
                }
            }
            
            Log::info("Chat found/created: {$chat->uuid}");
            
            // Get messages
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                ->orderBy('created_at', 'asc')
                ->get();
            
            Log::info("Found " . $messages->count() . " messages");
            
            // Transform messages
            $transformedMessages = $messages->map(function($message) {
                $senderInfo = null;
                
                if ($message->sender_type === 'user') {
                    $user = User::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $user ? [
                        'id' => $user->id_user,
                        'name' => $user->nama_user,
                        'type' => 'user'
                    ] : null;
                } elseif ($message->sender_type === 'admin') {
                    $admin = Admin::where('id_auth', $message->sender_id)->first();
                    $senderInfo = $admin ? [
                        'id' => $admin->id_admin,
                        'name' => $admin->nama_admin,
                        'type' => 'admin'
                    ] : null;
                }
                
                return [
                    'id' => $message->id,
                    'uuid' => $message->uuid,
                    'chat_uuid' => $message->chat_uuid,
                    'sender_id' => $message->sender_id,
                    'sender_type' => $message->sender_type,
                    'sender_info' => $senderInfo,
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'file_url' => $message->file_url,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ];
            });
            
            // Mark admin messages as read
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            Log::info("=== GET MESSAGES BY ORDER ID END ===");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan pesan',
                'messages' => $transformedMessages,
                'chat_info' => [
                    'chat_uuid' => $chat->uuid,
                    'pesanan_uuid' => $pesananUuid,
                    'order_id' => $orderId
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages by order ID: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mendapatkan pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send message by pesanan UUID - untuk kompatibilitas dengan aplikasi Flutter
     */
    public function sendMessageByPesanan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pesanan_uuid' => 'required|string',
                'message' => 'required|string',
                'message_type' => 'sometimes|in:text,image,file',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            $orderId = $request->pesanan_uuid;
            Log::info("Sending message for order: $orderId");
            
            // Gunakan strategi pencarian yang sama seperti getMessagesByOrderId
            $pesananUuid = null;
            $chat = null;
            
            // 1. Coba langsung sebagai UUID pesanan
            $pesanan = Pesanan::where('uuid', $orderId)->first();
            if ($pesanan && $pesanan->id_user == $user->id_user) {
                $pesananUuid = $orderId;
            }
            
            // 2. Coba cari di tabel transaksi
            if (!$pesananUuid) {
                $transaksi = Transaksi::where('order_id', $orderId)->first();
                if ($transaksi) {
                    $pesanan = Pesanan::where('uuid', $transaksi->pesanan_uuid)
                        ->where('id_user', $user->id_user)
                        ->first();
                    if ($pesanan) {
                        $pesananUuid = $pesanan->uuid;
                    }
                }
            }
            
            // 3. Coba cari chat yang sudah ada
            if (!$pesananUuid) {
                $chat = Chat::where('pesanan_uuid', $orderId)
                    ->where('user_id', $user->id_user)
                    ->first();
                if ($chat) {
                    $pesananUuid = $orderId;
                }
            }
            
            // Jika tidak ketemu sama sekali, return error
            if (!$pesananUuid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat untuk pesanan ini tidak ditemukan'
                ], 404);
            }
            
            // Cari atau buat chat
            if (!$chat) {
                $chat = Chat::where('pesanan_uuid', $pesananUuid)
                    ->where('user_id', $user->id_user)
                    ->first();
            }
            
            if (!$chat) {
                try {
                    $chat = $this->createChatForOrder($pesananUuid, $user->id_user);
                } catch (\Exception $e) {
                    Log::error('Error creating chat: ' . $e->getMessage());
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Gagal membuat chat untuk pesanan ini'
                    ], 500);
                }
            }

            // Create message
            $chatMessage = new ChatMessage();
            $chatMessage->uuid = Str::uuid();
            $chatMessage->chat_uuid = $chat->uuid;
            $chatMessage->sender_id = $userId;
            $chatMessage->sender_type = 'user';
            $chatMessage->message = $request->message;
            $chatMessage->message_type = $request->message_type ?? 'text';
            $chatMessage->file_url = $request->file_url;
            $chatMessage->save();
            
            // Update last message
            $lastMessage = $request->message_type === 'image' ? '📷 Gambar' : $request->message;
            $chat->last_message = substr($lastMessage, 0, 50);
            $chat->unread_count = ($chat->unread_count ?? 0) + 1;
            $chat->updated_at = now();
            $chat->save();
            
            Log::info("Message sent successfully for order: $orderId");
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim',
                'data' => $chatMessage
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending message by pesanan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'sometimes|string|exists:chats,uuid',
                'order_id' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $chat = null;
            
            if ($request->has('order_id')) {
                $orderId = $request->order_id;
                
                $pesananUuid = null;
                
                $pesanan = Pesanan::where('uuid', $orderId)->first();
                if ($pesanan && $pesanan->id_user == $user->id_user) {
                    $pesananUuid = $orderId;
                }
                
                if (!$pesananUuid) {
                    $transaksi = Transaksi::where('order_id', $orderId)->first();
                    if ($transaksi) {
                        $pesanan = Pesanan::where('uuid', $transaksi->pesanan_uuid)
                            ->where('id_user', $user->id_user)
                            ->first();
                        if ($pesanan) {
                            $pesananUuid = $pesanan->uuid;
                        }
                    }
                }
                
                if (!$pesananUuid) {
                    $chat = Chat::where('pesanan_uuid', $orderId)
                        ->where('user_id', $user->id_user)
                        ->first();
                    if ($chat) {
                        $pesananUuid = $orderId;
                    }
                }
                
                if (!$chat && $pesananUuid) {
                    $chat = Chat::where('pesanan_uuid', $pesananUuid)
                        ->where('user_id', $user->id_user)
                        ->first();
                }
            } else {
                $chat = Chat::where('uuid', $request->chat_uuid)
                       ->where('user_id', $user->id_user)
                       ->first();
            }
            
            if (!$chat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat tidak ditemukan atau Anda tidak memiliki akses'
                ], 404);
            }

            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            $chat->unread_count = 0;
            $chat->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil ditandai telah dibaca'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai pesan telah dibaca: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get or create chat room for an order
     */
    public function getOrCreateChatForOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'sometimes|string',
                'pesanan_uuid' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                Log::warning("User not found with auth_id: $userId");
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            // PERBAIKI: Ambil order_id dari request, fallback ke pesanan_uuid
            $orderId = $request->order_id ?? $request->pesanan_uuid;
            
            if (!$orderId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter order_id atau pesanan_uuid diperlukan'
                ], 400);
            }
            
            Log::info("Looking for order with ID/UUID: $orderId");
            
            // Variabel untuk menyimpan UUID pesanan yang valid
            $pesananUuid = null;
            
            // Cek apakah ini UUID pesanan yang valid
            $pesanan = Pesanan::where('uuid', $orderId)->first();
            if ($pesanan) {
                Log::info("Found pesanan directly with UUID: $orderId");
                $pesananUuid = $orderId;
            } 
            // Jika bukan UUID, coba cari di tabel transaksi
            else {
                Log::info("Not a direct UUID match, checking transaksi table");
                
                // Coba cari di tabel transaksi (untuk ID pesanan pendek)
                $transaksi = Transaksi::where('order_id', $orderId)->first();
                
                if ($transaksi) {
                    Log::info("Found transaksi with order_id: $orderId");
                    
                    // Ambil pesanan dari transaksi
                    $pesanan = Pesanan::where('uuid', $transaksi->pesanan_uuid)->first();
                    
                    if ($pesanan) {
                        Log::info("Found pesanan from transaksi: {$pesanan->uuid}");
                        $pesananUuid = $pesanan->uuid;
                    } else {
                        Log::warning("Transaksi found but pesanan does not exist");
                    }
                } else {
                    Log::warning("No transaksi found with order_id: $orderId");
                    
                    // Coba cari pesanan dengan UUID yang mengandung ID pesanan
                    $pesanan = Pesanan::where('uuid', 'like', "%$orderId%")->first();
                    
                    if ($pesanan) {
                        Log::info("Found pesanan with partial UUID match: {$pesanan->uuid}");
                        $pesananUuid = $pesanan->uuid;
                    } else {
                        Log::warning("Could not find any pesanan matching: $orderId");
                    }
                }
            }
            
            // Jika pesanan tidak ditemukan sama sekali, buat chat dummy
            if (!$pesananUuid) {
                Log::warning("Could not find any valid pesanan UUID for: $orderId, creating dummy chat");
                
                // Generate dummy chat data
                $dummyChat = [
                    'uuid' => Str::uuid()->toString(),
                    'user_id' => $user->id_user,
                    'admin_id' => 1, // Default admin ID
                    'pesanan_uuid' => $orderId, // Use the original ID
                    'last_message' => 'Chat dibuat',
                    'unread_count' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Generate dummy welcome message
                $dummyMessages = [
                    [
                        'uuid' => Str::uuid()->toString(),
                        'chat_uuid' => $dummyChat['uuid'],
                        'sender_id' => $dummyChat['admin_id'],
                        'sender_type' => 'system',
                        'message' => "Pesanan dengan ID $orderId tidak ditemukan. Silakan periksa ID pesanan Anda atau hubungi customer service.",
                        'message_type' => 'text',
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ];
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dummy chat created',
                    'data' => [
                        'chat_id' => $dummyChat['uuid'],
                        'chat' => $dummyChat
                    ],
                    'messages' => $dummyMessages
                ], 200);
            }
            
            // Cek apakah chat sudah ada untuk pesanan ini
            $existingChat = Chat::where('pesanan_uuid', $pesananUuid)
                           ->where('user_id', $user->id_user)
                           ->first();
            
            if ($existingChat) {
                Log::info("Found existing chat for pesanan $pesananUuid");
                
                // Ambil pesan untuk chat ini
                $messages = ChatMessage::where('chat_uuid', $existingChat->uuid)
                            ->orderBy('created_at', 'asc')
                            ->get();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Chat room already exists',
                    'data' => [
                        'chat_id' => $existingChat->uuid,
                        'chat' => $existingChat
                    ],
                    'messages' => $messages
                ]);
            }
            
            Log::info("Creating new chat for pesanan $pesananUuid");
            // Buat chat room baru dengan admin yang ditugaskan
            $chat = $this->createChatForOrder($pesananUuid, $user->id_user);
            
            // Ambil pesan untuk chat ini
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                        ->orderBy('created_at', 'asc')
                        ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Chat room created successfully',
                'data' => [
                    'chat_id' => $chat->uuid,
                    'chat' => $chat
                ],
                'messages' => $messages
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating chat for order: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create chat room: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to create a chat room for an order
     * This can be called from other controllers
     */
    public function createChatForOrder($pesananUuid, $userId)
    {
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Get pesanan details
            $pesanan = Pesanan::where('uuid', $pesananUuid)->first();
            
            if (!$pesanan) {
                throw new \Exception('Pesanan tidak ditemukan');
            }
            
            // PERBAIKAN: Cari admin dengan email adminchat@gmail.com
            $adminAuth = \App\Models\Auth::where('email', 'adminchat@gmail.com')
                        ->where('role', 'admin_chat')
                    ->first();
            
            if (!$adminAuth) {
                // Jika tidak ada, cari admin_chat role lainnya
                $adminAuth = \App\Models\Auth::where('role', 'admin_chat')->first();
            }
            
            if (!$adminAuth) {
                // Fallback ke admin biasa
                $adminAuth = \App\Models\Auth::where('role', 'admin')->first();
            }
            
            if (!$adminAuth) {
                throw new \Exception('Tidak ada admin yang tersedia');
            }
            
            // Cari admin berdasarkan auth
            $admin = Admin::where('id_auth', $adminAuth->id_auth)->first();
            
            if (!$admin) {
                throw new \Exception('Data admin tidak ditemukan');
            }
            
            Log::info("Assigning chat to admin: {$admin->nama_admin} (ID: {$admin->id_admin})");
            
            // Get jasa details for welcome message
            $jasa = Jasa::find($pesanan->id_jasa);
            $paketJasa = PaketJasa::find($pesanan->id_paket_jasa);
            
            // Create new chat room
            $chat = new Chat();
            $chat->uuid = Str::uuid();
            $chat->user_id = $userId;
            $chat->admin_id = $admin->id_admin; // Pastikan menggunakan id_admin yang benar
            $chat->pesanan_uuid = $pesananUuid;
            $chat->last_message = 'Chat dibuat';
            $chat->unread_count = 0;
            $chat->save();
            
            // Create welcome message
            $welcomeMessage = new ChatMessage();
            $welcomeMessage->uuid = Str::uuid();
            $welcomeMessage->chat_uuid = $chat->uuid;
            $welcomeMessage->sender_id = $adminAuth->id_auth; // Gunakan id_auth untuk sender_id
            $welcomeMessage->sender_type = 'admin';
            
            $kategoriJasa = $jasa ? ucfirst($jasa->kategori) : 'Jasa';
            $kelasPaket = $paketJasa ? ucfirst($paketJasa->kelas_jasa) : '';
            
            $welcomeMessage->message = "Halo! Admin TATA siap membantu Anda terkait pesanan Desain {$kategoriJasa} paket {$kelasPaket} #{$pesananUuid}";
            $welcomeMessage->message_type = 'text';
            $welcomeMessage->is_read = false;
            $welcomeMessage->save();
            
            // Create second welcome message with instructions
            $instructionMessage = new ChatMessage();
            $instructionMessage->uuid = Str::uuid();
            $instructionMessage->chat_uuid = $chat->uuid;
            $instructionMessage->sender_id = $adminAuth->id_auth; // Gunakan id_auth untuk sender_id
            $instructionMessage->sender_type = 'admin';
            $instructionMessage->message = "Silahkan sampaikan kebutuhan atau pertanyaan Anda terkait pesanan ini. Kami akan membantu sebaik mungkin.";
            $instructionMessage->message_type = 'text';
            $instructionMessage->is_read = false;
            $instructionMessage->save();
            
            // Update chat's last message
            $chat->last_message = $welcomeMessage->message;
            $chat->save();
            
            Log::info("Chat created successfully with UUID: {$chat->uuid}");
            
            DB::commit();
            return $chat;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating chat for order: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirm payment for an order by admin
     */
    public function confirmPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pesanan_uuid' => 'required|string|exists:pesanan,uuid',
                'status' => 'required|in:confirm,reject',
                'message' => 'nullable|string',
                'rejection_reason' => 'required_if:status,reject|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $adminId = Auth::id();
            $admin = Admin::where('id_auth', $adminId)->first();

            if (!$admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            // Get pesanan
            $pesanan = Pesanan::where('uuid', $request->pesanan_uuid)->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Get transaksi
            $transaksi = Transaksi::where('id_pesanan', $pesanan->id_pesanan)
                ->where('status_transaksi', 'menunggu_konfirmasi')
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan atau status tidak valid'
                ], 404);
            }

            // Begin transaction
            DB::beginTransaction();

            try {
                if ($request->status == 'confirm') {
                    // Update transaksi
                    $transaksi->status_transaksi = 'lunas';
                    $transaksi->confirmed_at = now();
                    $transaksi->admin_notes = $request->message ?? 'Pembayaran dikonfirmasi oleh admin';
                    $transaksi->save();

                    // Update pesanan
                    $pesanan->status_pesanan = 'diproses';
                    $pesanan->confirmed_at = now();
                    $pesanan->save();

                    // Send message to chat
                    $chatMessage = "✅ Pembayaran telah dikonfirmasi. Status pesanan berubah menjadi DIPROSES.";
                    if ($request->message) {
                        $chatMessage .= "\n\n" . $request->message;
                    }

                } else {
                    // Reject payment
                    $transaksi->status_transaksi = 'ditolak';
                    $transaksi->admin_notes = $request->rejection_reason;
                    $transaksi->save();

                    // Update pesanan
                    $pesanan->status_pesanan = 'dibatalkan';
                    $pesanan->save();

                    // Send message to chat
                    $chatMessage = "❌ Pembayaran ditolak. Status pesanan berubah menjadi DIBATALKAN.\n\nAlasan: " . $request->rejection_reason;
                }

                // Send notification to chat
                $chat = Chat::where('pesanan_uuid', $request->pesanan_uuid)->first();
                
                if ($chat) {
                    $chatNotif = new ChatMessage();
                    $chatNotif->uuid = Str::uuid();
                    $chatNotif->chat_uuid = $chat->uuid;
                    $chatNotif->sender_id = $admin->id_admin;
                    $chatNotif->sender_type = 'admin';
                    $chatNotif->message = $chatMessage;
                    $chatNotif->message_type = 'text';
                    $chatNotif->is_read = false;
                    $chatNotif->save();
                    
                    // Update last message
                    $chat->last_message = $chatMessage;
                    $chat->updated_at = now();
                    $chat->save();
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => $request->status == 'confirm' ? 'Pembayaran berhasil dikonfirmasi' : 'Pembayaran berhasil ditolak',
                    'data' => [
                        'pesanan' => $pesanan,
                        'transaksi' => $transaksi
                    ]
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error confirming payment: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification for chat messages via FCM
     */
    public function sendNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|string',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            // Get the chat record
            $chat = Chat::where('uuid', $request->chat_id)->first();
            
            if (!$chat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }
            
            // Get recipient (if sender is user, recipient is admin, and vice versa)
            $recipientType = ($userId == $chat->user_id) ? 'admin' : 'user';
            $recipientId = ($recipientType == 'admin') ? $chat->admin_id : $chat->user_id;
            
            // Get recipient's FCM token
            $recipientToken = null;
            
            if ($recipientType == 'admin') {
                $admin = Admin::find($chat->admin_id);
                if ($admin) {
                    $auth = $admin->auth;
                    if ($auth) {
                        $recipientToken = $auth->fcm_token;
                    }
                }
            } else {
                $recipient = User::find($chat->user_id);
                if ($recipient) {
                    $auth = $recipient->auth;
                    if ($auth) {
                        $recipientToken = $auth->fcm_token;
                    }
                }
            }
            
            if (!$recipientToken) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Recipient has no FCM token registered'
                ]);
            }
            
            // Get sender info
            $senderName = $user->name;
            $orderReference = $chat->pesanan_uuid ?? null;
            
            // Prepare notification data
            $notificationTitle = $orderReference 
                ? "Pesan baru untuk pesanan #{$orderReference}" 
                : "Pesan baru dari {$senderName}";
                
            $notificationBody = substr($request->message, 0, 100);
            if (strlen($request->message) > 100) {
                $notificationBody .= '...';
            }
            
            // Send FCM notification using ChatService
            app(\App\Services\ChatService::class)->sendNotification(
                $recipientId, 
                [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'order_id' => $orderReference ?? '',
                    'chat_id' => $request->chat_id
                ]
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification sent successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error sending chat notification: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync message from Firestore to Laravel Database
     */
    public function syncMessage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_uuid' => 'required|string',
                'message' => 'required|string',
                'sender_type' => 'required|in:user,admin',
                'message_type' => 'sometimes|in:text,image,file',
                'file_url' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Cek apakah chat exists
            $chat = Chat::where('uuid', $request->chat_uuid)->first();
            
            if (!$chat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }

            // Simpan message ke database Laravel
            $chatMessage = new ChatMessage();
            $chatMessage->uuid = Str::uuid();
            $chatMessage->chat_uuid = $request->chat_uuid;
            $chatMessage->sender_id = $user->id_user; // Gunakan id_user bukan id_auth
            $chatMessage->sender_type = $request->sender_type;
            $chatMessage->message = $request->message;
            $chatMessage->message_type = $request->message_type ?? 'text';
            $chatMessage->file_url = $request->file_url;
            $chatMessage->is_read = false;
            $chatMessage->save();

            // Update chat's last message
            $chat->last_message = substr($request->message, 0, 50);
            $chat->unread_count = ($chat->unread_count ?? 0) + 1;
            $chat->updated_at = now();
            $chat->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Message synced successfully',
                'data' => $chatMessage
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing message: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to sync message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug method to see exact response structure
     */
    public function debugChatList(Request $request)
    {
        try {
            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            $chats = Chat::with(['admin', 'pesanan'])
                    ->where('user_id', $user->id_user)
                    ->orderBy('updated_at', 'desc')
                    ->get();
            
            // ✅ DEBUG: Show exact data types
            $debugChats = $chats->map(function($chat) {
                return [
                    'id' => $chat->id,
                    'id_type' => gettype($chat->id),
                    'uuid' => $chat->uuid,
                    'user_id' => $chat->user_id,
                    'user_id_type' => gettype($chat->user_id),
                    'admin_id' => $chat->admin_id,
                    'admin_id_type' => gettype($chat->admin_id),
                    'pesanan_uuid' => $chat->pesanan_uuid,
                    'last_message' => $chat->last_message,
                    'unread_count' => $chat->unread_count,
                    'unread_count_type' => gettype($chat->unread_count),
                    'created_at' => $chat->created_at,
                    'updated_at' => $chat->updated_at,
                    'raw_data' => $chat->toArray()
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Debug chat list',
                'data' => $debugChats,
                'user_info' => [
                    'id_auth' => $userId,
                    'id_user' => $user->id_user,
                    'nama_user' => $user->nama_user
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Debug error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a direct chat with admin including product context
     */
    public function createDirectChat(Request $request)
    {
        try {
            Log::info('createDirectChat called with data:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'context_type' => 'required|string|in:product_info,general',
                'context_data' => 'required_if:context_type,product_info|array',
                'initial_message' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed: ' . $validator->errors()->first());
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = Auth::id();
            Log::info('User ID from Auth: ' . $userId);
            
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                Log::error('User not found for id_auth: ' . $userId);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            Log::info('Found user: ' . $user->nama_user . ' (ID: ' . $user->id_user . ')');
            
            // Find available admin (admin_chat role)
            $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                    ->where('auth.role', 'admin_chat')
                    ->inRandomOrder()
                    ->first();
            
            if (!$admin) {
                Log::info('No admin_chat found, falling back to any admin');
                // Fallback to any admin if no admin_chat found
                $admin = Admin::inRandomOrder()->first();
                
                if (!$admin) {
                    Log::error('No admin available');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tidak ada admin yang tersedia'
                    ], 500);
                }
            }
            
            Log::info('Selected admin: ' . $admin->nama_admin . ' (ID: ' . $admin->id_admin . ')');
            
            // Create new chat room
            $chat = new Chat();
            $chat->uuid = Str::uuid();
            $chat->user_id = $user->id_user;
            $chat->admin_id = $admin->id_admin;
            $chat->last_message = 'Chat dibuat';
            $chat->unread_count = 0;
            $chat->save();
            
            Log::info('Created chat room with UUID: ' . $chat->uuid);
            
            // Format product context information if available
            $contextMessage = "";
            if ($request->context_type == 'product_info' && !empty($request->context_data)) {
                $productData = $request->context_data;
                $contextMessage = "--- Info Produk ---\n";
                
                if (isset($productData['title'])) {
                    $contextMessage .= "Paket: " . $productData['title'] . "\n";
                }
                
                if (isset($productData['jenis_pesanan'])) {
                    $contextMessage .= "Jenis: " . $productData['jenis_pesanan'] . "\n";
                }
                
                if (isset($productData['price'])) {
                    $contextMessage .= "Harga: " . $productData['price'] . "\n";
                }
                
                if (isset($productData['duration'])) {
                    $contextMessage .= "Durasi: " . $productData['duration'] . "\n";
                }
                
                if (isset($productData['revision'])) {
                    $contextMessage .= "Revisi: " . $productData['revision'] . "\n";
                }
                
                $contextMessage .= "-------------------\n\n";
            }
            
            // Create context message from admin
            if (!empty($contextMessage)) {
                $adminContextMessage = new ChatMessage();
                $adminContextMessage->uuid = Str::uuid();
                $adminContextMessage->chat_uuid = $chat->uuid;
                $adminContextMessage->sender_id = $admin->id_auth; // Gunakan id_auth
                $adminContextMessage->sender_type = 'admin';
                $adminContextMessage->message = $contextMessage;
                $adminContextMessage->message_type = 'text';
                $adminContextMessage->is_read = false;
                $adminContextMessage->save();
                
                Log::info('Added context message from admin');
            }
            
            // Create welcome message from admin
            $welcomeMessage = new ChatMessage();
            $welcomeMessage->uuid = Str::uuid();
            $welcomeMessage->chat_uuid = $chat->uuid;
            $welcomeMessage->sender_id = $admin->id_auth; // Gunakan id_auth
            $welcomeMessage->sender_type = 'admin';
            $welcomeMessage->message = "Halo! Ada yang bisa kami bantu terkait produk ini?";
            $welcomeMessage->message_type = 'text';
            $welcomeMessage->is_read = false;
            $welcomeMessage->save();
            
            Log::info('Added welcome message from admin');
            
            // Create initial message from user if provided
            if ($request->has('initial_message') && !empty($request->initial_message)) {
                $userMessage = new ChatMessage();
                $userMessage->uuid = Str::uuid();
                $userMessage->chat_uuid = $chat->uuid;
                $userMessage->sender_id = $user->id_auth; // Gunakan id_auth
                $userMessage->sender_type = 'user';
                $userMessage->message = $request->initial_message;
                $userMessage->message_type = 'text';
                $userMessage->is_read = false;
                $userMessage->save();
                
                // Update chat's last message
                $chat->last_message = $request->initial_message;
                $chat->save();
                
                Log::info('Added initial message from user');
            } else {
                // Update chat's last message with admin's welcome
                $chat->last_message = $welcomeMessage->message;
                $chat->save();
                
                Log::info('No initial user message, using admin welcome as last message');
            }
            
            Log::info('Direct chat creation completed successfully');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Chat langsung berhasil dibuat',
                'data' => [
                    'chat_id' => $chat->uuid,
                    'admin_name' => $admin->nama_admin,
                ]
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating direct chat: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat chat: ' . $e->getMessage()
            ], 500);
        }
    }
} 