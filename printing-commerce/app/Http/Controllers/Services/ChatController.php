<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Models\User;
use App\Models\Order;
use App\Models\Pesanan;
// Hapus atau komentari class yang tidak ada
// use App\Models\ChatMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Google\Client as GoogleClient;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    private function assignEditor($sender_id, $pesanan, $editor){
        // // Send notification message to chat
            $isRevision = $pesanan->chatMessages()->where(function($q) {
                $q->where('message', 'like', '%revisi%')
                  ->orWhere('message', 'like', '%revision%')
                  ->orWhere('message', 'like', '%perbaikan%');
            })->exists();

            $assignmentMessage = $isRevision 
                ? "Editor {$editor->nama_editor} telah ditugaskan untuk menangani revisi Anda."
                : "Editor {$editor->nama_editor} telah ditugaskan untuk mengerjakan pesanan Anda.";
                
            if ($pesanan->notes) {
                $assignmentMessage .= " Catatan: " . $pesanan->notes;
            }

            // Komentari bagian yang menggunakan ChatMessage
            /*
            ChatMessage::create([
                'uuid' => Str::uuid(),
                'message' => $assignmentMessage,
                'sender_type' => 'admin',
                'sender_id' => $sender_id,
                'id_pesanan' => $pesanan->id_pesanan,
                'created_at' => now()
            ]);
            */
    // // Send completion message
            // $completionMessage = "Revisi telah selesai dikerjakan dan pesanan Anda sudah siap.";
            // if ($request->input('catatan_editor')) {
            //     $completionMessage .= " Catatan: " . $request->catatan_editor;
            // }

            // ChatMessage::create([
            //     'uuid' => Str::uuid(),
            //     'message' => $completionMessage,
            //     'sender_type' => 'admin',
            //     'sender_id' => auth()->id(),
            //     'id_pesanan' => $pesanan->id_pesanan,
            //     'created_at' => now()
            // ]);
        }

    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'receiver_id' => 'required|string',
                'message' => 'required_without:image|string|nullable',
                'image' => 'nullable|image|max:2048', // 2MB Max
                'order_id' => 'required|string'
            ]);

            // Handle image upload if present
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->chatService->uploadImage($request->file('image'));
            }

            // Create message data
            $messageData = [
                'sender_id' => auth()->id(),
                'receiver_id' => $validated['receiver_id'],
                'message' => $validated['message'] ?? '',
                'image_url' => $imageUrl,
                'order_id' => $validated['order_id'],
                'timestamp' => now()->timestamp,
                'read' => false
            ];

            // Save to Firebase
            $messageId = $this->chatService->saveMessage($messageData);

            // Send FCM notification
            $this->chatService->sendNotification($validated['receiver_id'], [
                'title' => 'New Message',
                'body' => $validated['message'] ?? 'Sent you an image',
                'type' => 'chat',
                'order_id' => $validated['order_id']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => [
                    'message_id' => $messageId,
                    'timestamp' => $messageData['timestamp']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getMessages(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
                'order_id' => 'required|string',
                'last_timestamp' => 'nullable|integer'
            ]);

            // Get messages from Firebase
            $messages = $this->chatService->getMessages(
                $validated['user_id'], 
                $validated['order_id'],
                $validated['last_timestamp'] ?? null
            );

            // Mark messages as read
            if (!empty($messages)) {
                $this->chatService->markAsRead(
                    $validated['user_id'],
                    auth()->id(),
                    $validated['order_id']
                );
            }

            return response()->json([
                'status' => 'success',
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|string',
                'order_id' => 'required|string'
            ]);

            $this->chatService->markAsRead(
                $validated['user_id'],
                auth()->id(),
                $validated['order_id']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Messages marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['message' => 'Device token updated successfully']);
    }

    public function sendFcmNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = \App\Models\User::find($request->user_id);
        $fcm = $user->fcm_token;

        if (!$fcm) {
            return response()->json(['message' => 'User does not have a device token'], 400);
        }

        $title = $request->title;
        $description = $request->body;
        $projectId = config('services.fcm.project_id'); # INSERT COPIED PROJECT ID

        $credentialsFilePath = Storage::path('app/json/file.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }
    /**
     * ADMIN: Send revision response via chat
     */
    public function sendRevisionResponse(Request $request, $uuid){
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:1000',
                'action' => 'required|in:accept,reject,request_clarification'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $pesanan = Pesanan::where('uuid', $uuid)->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Send response message
            /*
            ChatMessage::create([
                'uuid' => Str::uuid(),
                'message' => $request->message,
                'sender_type' => 'admin',
                'sender_id' => auth()->id(),
                'id_pesanan' => $pesanan->id_pesanan,
                'created_at' => now()
            ]);
            */

            // Update pesanan status based on action
            $statusUpdate = [];
            switch ($request->action) {
                case 'accept':
                    $statusUpdate['status'] = 'dikerjakan';
                    break;
                case 'reject':
                    $statusUpdate['status'] = 'selesai';
                    break;
                case 'request_clarification':
                    // Status remains the same, just asking for clarification
                    break;
            }

            if (!empty($statusUpdate)) {
                $pesanan->update($statusUpdate);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Respon revisi berhasil dikirim',
                'data' => [
                    'pesanan' => $pesanan->fresh(),
                    'action_taken' => $request->action
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim respon: ' . $e->getMessage()
            ], 500);
        }
    }
}