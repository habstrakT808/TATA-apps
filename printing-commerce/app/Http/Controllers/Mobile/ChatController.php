<?php
namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Admin;
use App\Models\Pesanan;
use App\Models\Jasa;
use App\Models\PaketJasa;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

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
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim',
                'data' => $chatMessage
            ]);
            
        } catch (\Exception $e) {
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
            
            // Get messages
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                       ->orderBy('created_at', 'desc')
                       ->paginate($limit, ['*'], 'page', $page);
            
            // Mark messages as read if sent by admin
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan pesan',
                'data' => $messages
            ]);
            
        } catch (\Exception $e) {
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
            
            $path = $file->storeAs('chat_files', $fileName, 'public');
            $url = asset('storage/' . $path);
            
            return response()->json([
                'status' => 'success',
                'message' => 'File berhasil diunggah',
                'data' => [
                    'file_url' => $url,
                    'file_name' => $fileName
                ]
            ]);
            
        } catch (\Exception $e) {
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
            $userId = Auth::id();
            $user = User::where('id_auth', $userId)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
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
            
            // Get messages
            $messages = ChatMessage::where('chat_uuid', $chat->uuid)
                       ->orderBy('created_at', 'asc')
                       ->get();
            
            // Mark messages as read if sent by admin
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mendapatkan pesan',
                'messages' => $messages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting messages by pesanan: ' . $e->getMessage());
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
            
            $pesananUuid = $request->pesanan_uuid;
            
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
            $chat->last_message = substr($request->message, 0, 50);
            $chat->unread_count = ($chat->unread_count ?? 0) + 1;
            $chat->updated_at = now();
            $chat->save();
            
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
                'chat_uuid' => 'required|string|exists:chats,uuid',
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

            // Mark messages as read if sent by admin
            ChatMessage::where('chat_uuid', $chat->uuid)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            // Reset unread count
            $chat->unread_count = 0;
            $chat->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil ditandai telah dibaca'
            ]);
            
        } catch (\Exception $e) {
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
                'order_id' => 'required|string',
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
            
            $orderId = $request->order_id;
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
            
            // Coba cari admin dengan role admin_chat terlebih dahulu
            $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                    ->where('auth.role', 'admin_chat')
                    ->inRandomOrder()
                    ->first();
            
            // Jika tidak ada admin_chat, cari admin dengan role admin
            if (!$admin) {
                $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                        ->where('auth.role', 'admin')
                        ->inRandomOrder()
                        ->first();
            }
            
            // Jika masih tidak ada, cari admin dengan role apapun
            if (!$admin) {
                $admin = Admin::join('auth', 'admin.id_auth', '=', 'auth.id_auth')
                        ->inRandomOrder()
                        ->first();
            }
            
            // Jika masih tidak ada admin sama sekali, gunakan ID admin default
            if (!$admin) {
                Log::warning('No admin found, using default admin ID 1');
                $adminId = 1;
            } else {
                $adminId = $admin->id_admin;
            }
            
            // Get jasa details for welcome message
            $jasa = Jasa::find($pesanan->id_jasa);
            $paketJasa = PaketJasa::find($pesanan->id_paket_jasa);
            
            // Create new chat room
            $chat = new Chat();
            $chat->uuid = Str::uuid();
            $chat->user_id = $userId;
            $chat->admin_id = $adminId;
            $chat->pesanan_uuid = $pesananUuid;
            $chat->last_message = 'Chat dibuat';
            $chat->unread_count = 0;
            $chat->save();
            
            // Create welcome message
            $welcomeMessage = new ChatMessage();
            $welcomeMessage->uuid = Str::uuid();
            $welcomeMessage->chat_uuid = $chat->uuid;
            $welcomeMessage->sender_id = $adminId;
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
            $instructionMessage->sender_id = $adminId;
            $instructionMessage->sender_type = 'admin';
            $instructionMessage->message = "Silahkan sampaikan kebutuhan atau pertanyaan Anda terkait pesanan ini. Kami akan membantu sebaik mungkin.";
            $instructionMessage->message_type = 'text';
            $instructionMessage->is_read = false;
            $instructionMessage->save();
            
            // Update chat's last message
            $chat->last_message = $welcomeMessage->message;
            $chat->save();
            
            DB::commit();
            return $chat;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating chat for order: ' . $e->getMessage());
            throw $e;
        }
    }
} 