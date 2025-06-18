<?php

namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Illuminate\Support\Facades\Storage as LaravelStorage;
use App\Models\User;
use App\Models\Order;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class ChatService
{
    protected $messaging;
    protected $googleClient;
    protected $projectId;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
        $this->projectId = config('firebase.project_id');
        $this->initializeGoogleClient();
    }

    protected function initializeGoogleClient()
    {
        $credentialsFilePath = config('firebase.credentials.file');
        $this->googleClient = new GoogleClient();
        $this->googleClient->setAuthConfig($credentialsFilePath);
        $this->googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function getUserConversations($userId)
    {
        // Get conversations from Firebase
        $reference = $this->database->getReference('conversations/'.$userId);
        $snapshot = $reference->getSnapshot();
        $conversations = $snapshot->getValue() ?? [];

        // Get user and order details from MySQL
        $conversationDetails = [];
        foreach ($conversations as $conversationId => $conversation) {
            $otherUserId = $conversation['participants'][0] === $userId 
                ? $conversation['participants'][1] 
                : $conversation['participants'][0];

            $user = User::where('uuid', $otherUserId)->first();
            $order = Order::find($conversation['order_id']);

            if ($user && $order) {
                $conversationDetails[] = [
                    'id' => $conversationId,
                    'user' => [
                        'uuid' => $user->uuid,
                        'name' => $user->name,
                        'avatar' => $user->avatar
                    ],
                    'order' => [
                        'id' => $order->id,
                        'title' => $order->title,
                        'price' => $order->price
                    ],
                    'last_message' => $conversation['last_message'] ?? null,
                    'unread_count' => $conversation['unread_count'][$userId] ?? 0,
                    'updated_at' => $conversation['updated_at'] ?? null
                ];
            }
        }

        // Sort by last message timestamp
        usort($conversationDetails, function($a, $b) {
            return ($b['updated_at'] ?? 0) - ($a['updated_at'] ?? 0);
        });

        return $conversationDetails;
    }

    public function getMessages($userId, $orderId, $lastTimestamp = null)
    {
        $reference = $this->database->getReference('messages/'.$orderId);
        
        if ($lastTimestamp) {
            $reference = $reference->orderByChild('timestamp')
                                 ->startAfter($lastTimestamp);
        }

        $snapshot = $reference->getSnapshot();
        $messages = $snapshot->getValue() ?? [];

        // Convert to array and add any additional data needed
        return collect($messages)->map(function($message) {
            return [
                'id' => $message['id'],
                'sender_id' => $message['sender_id'],
                'message' => $message['message'] ?? '',
                'image_url' => $message['image_url'] ?? null,
                'timestamp' => $message['timestamp'],
                'read' => $message['read'] ?? false
            ];
        })->values()->all();
    }

    public function saveMessage($messageData)
    {
        // Generate unique message ID
        $messageId = uniqid('msg_');
        
        // Save message to Firebase
        $this->database->getReference('messages/'.$messageData['order_id'].'/'.$messageId)
            ->set($messageData);

        // Update conversation last message
        $this->updateConversation($messageData);

        // Send FCM notification
        $this->sendNotification($messageData['receiver_id'], [
            'title' => 'New Message',
            'body' => $messageData['message'] ?? 'Sent you an image',
            'order_id' => $messageData['order_id']
        ]);

        return $messageId;
    }

    protected function updateConversation($messageData)
    {
        $participants = [$messageData['sender_id'], $messageData['receiver_id']];
        sort($participants); // Ensure consistent order
        $conversationId = 'conv_'.$messageData['order_id'];

        // Update or create conversation
        $this->database->getReference('conversations/'.$participants[0].'/'.$conversationId)
            ->update([
                'participants' => $participants,
                'order_id' => $messageData['order_id'],
                'last_message' => [
                    'text' => substr($messageData['message'] ?? 'Sent an image', 0, 50),
                    'timestamp' => $messageData['timestamp']
                ],
                'updated_at' => $messageData['timestamp'],
                'unread_count' => [
                    $messageData['receiver_id'] => $this->database->getReference('conversations/'.$participants[0].'/'.$conversationId.'/unread_count/'.$messageData['receiver_id'])
                        ->getValue() + 1
                ]
            ]);

        // Mirror for other participant
        $this->database->getReference('conversations/'.$participants[1].'/'.$conversationId)
            ->update([
                'participants' => $participants,
                'order_id' => $messageData['order_id'],
                'last_message' => [
                    'text' => substr($messageData['message'] ?? 'Sent an image', 0, 50),
                    'timestamp' => $messageData['timestamp']
                ],
                'updated_at' => $messageData['timestamp'],
                'unread_count' => [
                    $messageData['receiver_id'] => $this->database->getReference('conversations/'.$participants[1].'/'.$conversationId.'/unread_count/'.$messageData['receiver_id'])
                        ->getValue() + 1
                ]
            ]);
    }

    public function markAsRead($senderId, $receiverId, $orderId)
    {
        $participants = [$senderId, $receiverId];
        sort($participants);
        $conversationId = 'conv_'.$orderId;

        // Reset unread count for receiver
        $this->database->getReference('conversations/'.$receiverId.'/'.$conversationId.'/unread_count/'.$receiverId)
            ->set(0);

        // Mark messages as read
        $reference = $this->database->getReference('messages/'.$orderId);
        $snapshot = $reference->orderByChild('sender_id')
                            ->equalTo($senderId)
                            ->getSnapshot();
        
        foreach ($snapshot->getValue() ?? [] as $messageId => $message) {
            if (!$message['read']) {
                $this->database->getReference('messages/'.$orderId.'/'.$messageId.'/read')
                    ->set(true);
            }
        }
    }

    public function uploadImage($file)
    {
        // Generate unique filename
        $filename = uniqid('chat_') . '.' . $file->getClientOriginalExtension();
        
        // Upload to Firebase Storage
        $bucket = $this->storage->getBucket();
        $bucket->upload(
            file_get_contents($file->getRealPath()),
            ['name' => 'chat_images/' . $filename]
        );

        // Get public URL
        return $bucket->object('chat_images/' . $filename)->signedUrl(new \DateTime('+ 10 years'));
    }

    public function sendNotification($userId, $data)
    {
        try {
            $user = User::where('uuid', $userId)->first();
            
            if (!$user || !$user->fcm_token) {
                return false;
            }

            // Refresh token and get access token
            $this->googleClient->refreshTokenWithAssertion();
            $token = $this->googleClient->getAccessToken();
            $accessToken = $token['access_token'];

            $headers = [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json'
            ];

            // Prepare notification payload
            $notificationData = [
                "message" => [
                    "token" => $user->fcm_token,
                    "notification" => [
                        "title" => $data['title'],
                        "body" => $data['body']
                    ],
                    "data" => [
                        "type" => "chat",
                        "order_id" => $data['order_id'] ?? '',
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                    ]
                ]
            ];

            $payload = json_encode($notificationData);

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                \Log::error('FCM Notification Error: ' . $err);
                return false;
            }

            return json_decode($response, true);

        } catch (\Exception $e) {
            \Log::error('FCM Notification Error: ' . $e->getMessage());
            return false;
        }
    }
} 