<?php

namespace App\Services;

use Google\Cloud\Firestore\FirestoreClient;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseService
{
    private $firestore;
    private $projectId;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        
        // Initialize Firestore (dengan gRPC)
        $this->firestore = new FirestoreClient([
            'projectId' => $this->projectId,
            'keyFilePath' => config('firebase.credentials_path')
        ]);
        
        Log::info('🔥 Firebase Service initialized (Medium Tutorial Style)', [
            'project_id' => $this->projectId
        ]);
    }

    /**
     * Get FCM Access Token (Tutorial Medium Style)
     */
    private function getFcmAccessToken()
    {
        try {
            $credentialsFilePath = config('firebase.credentials_path');
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();
            
            return $token['access_token'];
        } catch (Exception $e) {
            Log::error('Get FCM access token error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim push notification (FCM v1 - Tutorial Medium Style)
     */
    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        try {
            $accessToken = $this->getFcmAccessToken();
            if (!$accessToken) {
                return false;
            }

            $headers = [
                "Authorization: Bearer $accessToken",
                'Content-Type: application/json'
            ];

            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => $data
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                Log::error('FCM Curl Error: ' . $err);
                return false;
            } else {
                $result = json_decode($response, true);
                Log::info('🔔 FCM NOTIFICATION SENT (Medium Style)', [
                    'fcm_token' => substr($fcmToken, 0, 20) . '...',
                    'title' => $title,
                    'response' => $result
                ]);
                return $result;
            }
        } catch (Exception $e) {
            Log::error('FCM notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simpan pesan chat ke Firestore (REAL)
     */
    public function saveChatMessage($pesananUuid, $senderUuid, $senderType, $message)
    {
        try {
            $messageData = [
                'sender_uuid' => $senderUuid,
                'sender_type' => $senderType,
                'message' => $message,
                'timestamp' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                'created_at' => now()->toISOString()
            ];

            $docRef = $this->firestore
                ->collection('chats')
                ->document($pesananUuid)
                ->collection('messages')
                ->add($messageData);
            
            Log::info('💬 REAL CHAT MESSAGE SAVED', [
                'pesanan_uuid' => $pesananUuid,
                'message_id' => $docRef->id(),
                'sender' => $senderType
            ]);
            
            return $docRef->id();
        } catch (Exception $e) {
            Log::error('Save chat error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil pesan chat dari Firestore (REAL)
     */
    public function getChatMessages($pesananUuid, $limit = 50)
    {
        try {
            $documents = $this->firestore
                ->collection('chats')
                ->document($pesananUuid)
                ->collection('messages')
                ->orderBy('timestamp', 'ASC')
                ->limit($limit)
                ->documents();

            $messages = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $data = $document->data();
                    $data['id'] = $document->id();
                    $messages[] = $data;
                }
            }

            Log::info('📥 REAL CHAT MESSAGES RETRIEVED', [
                'pesanan_uuid' => $pesananUuid,
                'count' => count($messages)
            ]);
            
            return $messages;
        } catch (Exception $e) {
            Log::error('Get chat error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update info chat terakhir (REAL)
     */
    public function updateChatInfo($pesananUuid, $lastMessage, $senderType)
    {
        try {
            $this->firestore
                ->collection('chats')
                ->document($pesananUuid)
                ->set([
                    'last_message' => $lastMessage,
                    'last_sender' => $senderType,
                    'updated_at' => new \Google\Cloud\Core\Timestamp(new \DateTime())
                ], ['merge' => true]);

            Log::info('📝 REAL CHAT INFO UPDATED', [
                'pesanan_uuid' => $pesananUuid,
                'last_message' => $lastMessage
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error('Update chat info error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user FCM token di Firestore
     */
    public function updateUserToken($userUuid, $fcmToken)
    {
        try {
            $this->firestore->collection('users')
                ->document($userUuid)
                ->set([
                    'fcm_token' => $fcmToken,
                    'fcm_updated_at' => new \Google\Cloud\Core\Timestamp(new \DateTime()),
                    'last_seen' => new \Google\Cloud\Core\Timestamp(new \DateTime())
                ], ['merge' => true]);

            Log::info('✅ User FCM token updated in Firestore', [
                'user_uuid' => $userUuid,
                'fcm_token' => substr($fcmToken, 0, 20) . '...'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Update user FCM token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user FCM token dari Firestore (REAL)
     */
    public function getUserToken($userUuid)
    {
        try {
            $doc = $this->firestore->collection('users')
                ->document($userUuid)
                ->snapshot();

            if ($doc->exists()) {
                $data = $doc->data();
                return $data['fcm_token'] ?? null;
            }

            return null;
        } catch (Exception $e) {
            Log::error('Get FCM token error: ' . $e->getMessage());
            return null;
        }
    }
} 