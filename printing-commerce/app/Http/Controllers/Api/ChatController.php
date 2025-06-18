<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Kirim pesan text chat + FCM notification
     * POST /api/chat/send
     * Body: {"pesanan_uuid": "xxx", "message": "xxx"}
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'pesanan_uuid' => 'required|exists:pesanan,uuid',
                'message' => 'required|string|max:1000'
            ]);

            $user = auth()->user();
            $pesananUuid = $request->pesanan_uuid;
            $message = $request->message;

            // 1. Simpan chat message ke Firestore
            $messageId = $this->firebase->saveChatMessage(
                $pesananUuid,
                $user->uuid,
                'user',
                $message
            );

            if (!$messageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan pesan'
                ], 500);
            }

            // 2. Update chat info
            $this->firebase->updateChatInfo($pesananUuid, $message, 'user');

            // 3. Get admin FCM token dan kirim notification
            $adminToken = $this->getAdminFcmToken();
            if ($adminToken) {
                $this->firebase->sendNotification(
                    $adminToken,
                    'Pesan Baru dari ' . $user->name,
                    $message,
                    [
                        'pesanan_uuid' => $pesananUuid,
                        'message_id' => $messageId,
                        'sender_type' => 'user'
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message_id' => $messageId,
                'message' => 'Pesan terkirim'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil history chat
     */
    public function getMessages($pesananUuid)
    {
        // Cek apakah user berhak akses chat ini
        $pesanan = Pesanan::where('uuid', $pesananUuid)
            ->where('user_id', auth()->id())
            ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        $messages = $this->firebase->getChatMessages($pesananUuid);

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Update FCM Token (Handle Token Changes)
     * POST /api/chat/update-fcm-token
     * Body: {"fcm_token": "xxx", "device_id": "xxx", "device_type": "android"}
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
                'device_id' => 'nullable|string',
                'device_type' => 'nullable|string|in:android,ios'
            ]);

            $user = auth()->user();
            
            // 1. Update FCM token di MySQL (users table)
            $user->updateFcmToken(
                $request->fcm_token,
                $request->device_id,
                $request->device_type
            );
            
            // 2. Update FCM token di Firestore (untuk real-time chat)
            $firestoreSuccess = $this->firebase->updateUserToken(
                $user->uuid,
                $request->fcm_token
            );

            // 3. Log perubahan FCM token
            Log::info('🔑 FCM TOKEN UPDATED', [
                'user_uuid' => $user->uuid,
                'user_id' => $user->id,
                'device_id' => $request->device_id,
                'device_type' => $request->device_type,
                'firestore_sync' => $firestoreSuccess ? 'success' : 'failed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token berhasil diupdate',
                'data' => [
                    'mysql_updated' => true,
                    'firestore_updated' => $firestoreSuccess,
                    'updated_at' => $user->fcm_token_updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Update FCM token error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pesanan_uuid' => 'required|exists:pesanan,uuid',
                'message_ids' => 'required|array',
                'message_ids.*' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Validate user access
            $pesanan = Pesanan::where('uuid', $request->pesanan_uuid)->first();
            $user = User::where('id_auth', $request->user()->id_auth)->first();

            if ($pesanan->id_user !== $user->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke chat ini'
                ], 403);
            }

            // Update read status in Firestore
            foreach ($request->message_ids as $messageId) {
                $this->firebase->firestoreClient
                    ->collection('chats')
                    ->document($request->pesanan_uuid)
                    ->collection('messages')
                    ->document($messageId)
                    ->update([
                        ['path' => 'is_read', 'value' => true],
                        ['path' => 'read_at', 'value' => new \Google\Cloud\Core\Timestamp(new \DateTime())]
                    ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil ditandai sudah dibaca'
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as read error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai pesan sudah dibaca'
            ], 500);
        }
    }

    /**
     * Send notification to admin
     */
    private function sendNotificationToAdmin($pesanan, $messageData)
    {
        try {
            // Get admin FCM tokens (you can store these in Firestore or database)
            $adminTokens = $this->getAdminFcmTokens();
            
            if (!empty($adminTokens)) {
                $title = 'Pesan Baru dari ' . $messageData['sender_name'];
                $body = $messageData['message_type'] === 'image' 
                    ? 'Mengirim gambar' 
                    : substr($messageData['message'], 0, 100);

                $data = [
                    'type' => 'new_message',
                    'pesanan_uuid' => $pesanan->uuid,
                    'sender_uuid' => $messageData['sender_uuid'],
                    'message_type' => $messageData['message_type']
                ];

                $this->firebase->sendToMultiple($adminTokens, $title, $body, $data);
            }
        } catch (\Exception $e) {
            Log::error('Send admin notification error: ' . $e->getMessage());
        }
    }

    /**
     * Get admin FCM tokens (implement based on your admin system)
     */
    private function getAdminFcmTokens()
    {
        // You can implement this to get admin tokens from database or Firestore
        // For now, return empty array
        return [];
    }

    /**
     * ========================================
     * TESTING ENDPOINTS - UNTUK POSTMAN ONLY
     * ========================================
     */

    /**
     * Test Firebase Connection
     * POST /api/chat/test-connection
     */
    public function testConnection()
    {
        try {
            // Test dengan method yang sudah ada
            $messageId = $this->firebase->saveChatMessage(
                'test-connection',
                'system',
                'system',
                'Testing Firebase connection - ' . now()->toISOString()
            );

            if ($messageId) {
                return response()->json([
                    'success' => true,
                    'message' => 'Firebase connection berhasil!',
                    'message_id' => $messageId
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Firebase connection gagal'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase connection gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Save Chat Message (Tanpa Auth)
     * POST /api/chat/test-save
     * Body: {"pesanan_uuid": "test-123", "sender_uuid": "user-456", "message": "Hello test"}
     */
    public function testSaveMessage(Request $request)
    {
        try {
            $request->validate([
                'pesanan_uuid' => 'required|string',
                'sender_uuid' => 'required|string', 
                'message' => 'required|string'
            ]);

            $messageId = $this->firebase->saveChatMessage(
                $request->pesanan_uuid,
                $request->sender_uuid,
                'user',
                $request->message
            );

            if ($messageId) {
                return response()->json([
                    'success' => true,
                    'message_id' => $messageId,
                    'message' => 'Pesan berhasil disimpan ke Firestore'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pesan'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Get Chat Messages (Tanpa Auth)
     * GET /api/chat/test-get/{pesanan_uuid}
     */
    public function testGetMessages($pesananUuid)
    {
        try {
            $messages = $this->firebase->getChatMessages($pesananUuid);

            return response()->json([
                'success' => true,
                'pesanan_uuid' => $pesananUuid,
                'total_messages' => count($messages),
                'messages' => $messages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test FCM Notification (Tanpa Auth)
     * POST /api/chat/test-fcm
     * Body: {"fcm_token": "xxx", "title": "Test", "body": "Hello"}
     */
    public function testFcmNotification(Request $request)
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string',
                'title' => 'required|string',
                'body' => 'required|string'
            ]);

            $result = $this->firebase->sendNotification(
                $request->fcm_token,
                $request->title,
                $request->body,
                ['test' => 'true', 'source' => 'postman', 'timestamp' => now()->toISOString()] // FCM data harus string semua
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'FCM notification berhasil dikirim',
                    'result' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim FCM notification'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Test FCM Token untuk simulasi
     * GET /api/chat/generate-test-token
     */
    public function generateTestToken()
    {
        // Generate fake FCM token untuk testing (format seperti FCM token asli)
        $fakeToken = 'fakeToken_' . str_replace(['+', '/', '='], ['', '', ''], base64_encode(random_bytes(100)));
        
        return response()->json([
            'success' => true,
            'message' => 'Fake FCM token generated untuk testing',
            'fcm_token' => $fakeToken,
            'note' => 'Ini fake token. Untuk real testing, pakai FCM token dari Flutter app'
        ]);
    }

    /**
     * Simulasi Chat + FCM Notification
     * POST /api/chat/test-chat-with-fcm
     */
    public function testChatWithFcm(Request $request)
    {
        try {
            $request->validate([
                'pesanan_uuid' => 'required|string',
                'sender_uuid' => 'required|string',
                'message' => 'required|string',
                'fcm_token' => 'required|string'
            ]);

            // 1. Simpan chat message
            $messageId = $this->firebase->saveChatMessage(
                $request->pesanan_uuid,
                $request->sender_uuid,
                'user',
                $request->message
            );

            if (!$messageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan chat'
                ], 500);
            }

            // 2. Update chat info
            $this->firebase->updateChatInfo(
                $request->pesanan_uuid, 
                $request->message, 
                'user'
            );

            // 3. Kirim FCM notification
            $fcmResult = $this->firebase->sendNotification(
                $request->fcm_token,
                'Pesan Baru',
                $request->message,
                [
                    'pesanan_uuid' => $request->pesanan_uuid,
                    'message_id' => $messageId,
                    'sender_type' => 'user',
                    'action' => 'new_message'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Chat saved dan FCM notification sent',
                'data' => [
                    'message_id' => $messageId,
                    'fcm_result' => $fcmResult
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Firebase Service Instantiation Only
     * POST /api/chat/test-simple
     */
    public function testSimple()
    {
        try {
            // Cek apakah FirebaseService bisa di-instantiate
            $configCheck = [
                'project_id' => config('firebase.project_id'),
                'credentials_exists' => file_exists(config('firebase.credentials')),
                'credentials_path' => config('firebase.credentials')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Firebase service berhasil di-load',
                'config' => $configCheck
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get admin FCM token (dari database atau Firestore)
     */
    private function getAdminFcmToken()
    {
        // Option 1: Hardcode untuk testing
        // return 'admin-fcm-token-here';
        
        // Option 2: Dari database (admin users)
        $admin = User::where('role', 'admin')
                    ->whereNotNull('fcm_token')
                    ->where(function($query) {
                        $query->whereNull('fcm_token_updated_at')
                              ->orWhere('fcm_token_updated_at', '>', now()->subDays(30));
                    })
                    ->first();
        
        return $admin ? $admin->fcm_token : null;
    }

    /**
     * Get user FCM token by UUID
     */
    private function getUserFcmToken($userUuid)
    {
        $user = User::where('uuid', $userUuid)
                   ->whereNotNull('fcm_token')
                   ->first();
        
        // Check apakah token masih valid
        if ($user && $user->isFcmTokenValid()) {
            return $user->fcm_token;
        }
        
        return null;
    }

    /**
     * Send notification ke user berdasarkan pesanan
     */
    private function sendNotificationToUser($pesananUuid, $title, $body, $data = [])
    {
        try {
            // Get pesanan untuk dapat user_id
            $pesanan = \App\Models\Pesanan::where('uuid', $pesananUuid)->first();
            if (!$pesanan) {
                return false;
            }
            
            // Get user FCM token
            $user = User::find($pesanan->user_id);
            if (!$user || !$user->isFcmTokenValid()) {
                Log::warning('User FCM token not found or expired', [
                    'user_id' => $pesanan->user_id,
                    'pesanan_uuid' => $pesananUuid
                ]);
                return false;
            }
            
            // Send FCM notification
            return $this->firebase->sendNotification(
                $user->fcm_token,
                $title,
                $body,
                $data
            );
            
        } catch (\Exception $e) {
            Log::error('Send notification to user error: ' . $e->getMessage());
            return false;
        }
    }
} 