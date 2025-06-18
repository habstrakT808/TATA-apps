<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\UtilityController;
use App\Models\Admin;
use App\Models\Chat;
use App\Services\ChatService;
use App\Models\User;
use App\Models\Order;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    public function index()
    {
        return view('page.chat.index');
        return view('page.chat.index', compact('conversations'));
        // Get all chat conversations for current user
        $conversations = $this->chatService->getUserConversations(auth()->id());
    }

    public function show($uuid)
    {
        return view('page.chat.detail');
        // Get chat details from MySQL (user and order info)
        $chat = [
            'user' => User::where('uuid', $uuid)->first(),
            'order' => Order::where('user_uuid', $uuid)->latest()->first()
        ];

        // Get messages from Firebase
        $messages = $this->chatService->getMessages($uuid);

        return view('page.chat.detail', compact('chat', 'messages'));
    }

    public function showAll(Request $request){
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return view('page.chat.index',$dataShow);
    }
    public function showDetail(Request $request, $uuid){
        return view('page.chat.detail');
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
    }
}