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

class ChatController extends Controller
{
    public function showAll(Request $request){
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
            $adminId = $request->user()->id_admin ?? null;
            
            if (!$adminId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin ID tidak ditemukan'
                ], 400);
            }
            
            // Get chats assigned to this admin or chats without admin (for admin_chat role)
            $query = Chat::with(['user', 'pesanan'])
                ->where(function ($q) use ($adminId) {
                    $q->where('admin_id', $adminId)
                      ->orWhereNull('admin_id');
                })
                ->orderBy('updated_at', 'desc');
                
            if ($request->user()->role === 'admin_chat') {
                // For chat admins, show all chats
                $chats = $query->get();
            } else {
                // For others, only show assigned chats
                $chats = $query->where('admin_id', $adminId)->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $chats
            ]);
        } catch (\Exception $e) {
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
            
            $adminId = $request->user()->id_admin ?? null;
            $adminAuthId = $request->user()->id_auth ?? null;
            
            if (!$adminId || !$adminAuthId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin ID tidak ditemukan'
                ], 400);
            }
            
            $chat = Chat::where('uuid', $request->chat_uuid)->first();
            
            if (!$chat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat tidak ditemukan'
                ], 404);
            }
            
            // If chat doesn't have admin assigned, assign this admin
            if (!$chat->admin_id) {
                $chat->admin_id = $adminId;
                $chat->save();
            }
            
            // Create message
            $message = new ChatMessage();
            $message->uuid = Str::uuid();
            $message->chat_uuid = $request->chat_uuid;
            $message->sender_id = $adminAuthId;
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
            
            return response()->json([
                'success' => true,
                'data' => $message
            ]);
        } catch (\Exception $e) {
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
}