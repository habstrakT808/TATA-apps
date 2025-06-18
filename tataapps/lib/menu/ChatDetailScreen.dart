import 'package:flutter/material.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/services/ChatService.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:cloud_firestore/cloud_firestore.dart';

class ChatDetailScreen extends StatefulWidget {
  final String chatId;
  
  const ChatDetailScreen({required this.chatId, Key? key}) : super(key: key);
  
  @override
  _ChatDetailScreenState createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends State<ChatDetailScreen> {
  final ChatService _chatService = ChatService();
  final TextEditingController _messageController = TextEditingController();
  String? _userId;
  ChatModel? _chatData;
  bool _isLoading = true;
  
  @override
  void initState() {
    super.initState();
    _loadUserId();
    _loadChatData();
    _markMessagesAsRead();
  }
  
  Future<void> _loadUserId() async {
    final userData = await UserPreferences.getUser();
    setState(() {
      _userId = userData?['user']['id'];
    });
  }
  
  Future<void> _loadChatData() async {
    try {
      final chatDoc = await FirebaseFirestore.instance
          .collection('chats')
          .doc(widget.chatId)
          .get();
      
      if (chatDoc.exists) {
        setState(() {
          _chatData = ChatModel.fromFirestore(chatDoc);
          _isLoading = false;
        });
      } else {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      print('Error loading chat data: $e');
      setState(() {
        _isLoading = false;
      });
    }
  }
  
  void _markMessagesAsRead() {
    if (widget.chatId.isNotEmpty) {
      _chatService.markMessagesAsRead(widget.chatId, 'user');
    }
  }
  
  void _sendMessage() {
    if (_messageController.text.trim().isNotEmpty) {
      _chatService.sendMessage(
        widget.chatId,
        _messageController.text.trim(),
        'user'
      );
      _messageController.clear();
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Chat dengan Admin'),
        backgroundColor: CustomColors.primaryColor,
      ),
      body: Column(
        children: [
          // Order reference info if available
          if (_chatData?.orderReference != null && _chatData!.orderReference!.isNotEmpty)
            Container(
              padding: const EdgeInsets.all(8),
              color: Colors.grey.shade100,
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: Colors.grey.shade200,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Icon(
                      Icons.shopping_bag,
                      color: CustomColors.primaryColor,
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          'Pesanan #${_chatData!.orderReference}',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const Text(
                          'Chat terkait pesanan ini',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          
          // Messages list
          Expanded(
            child: StreamBuilder<List<MessageModel>>(
              stream: _chatService.getMessagesForChat(widget.chatId),
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                }
                
                if (!snapshot.hasData || snapshot.data!.isEmpty) {
                  return const Center(child: Text('Belum ada pesan'));
                }
                
                final messages = snapshot.data!;
                
                return ListView.builder(
                  padding: const EdgeInsets.all(12),
                  itemCount: messages.length,
                  itemBuilder: (context, index) {
                    final message = messages[index];
                    final isFromUser = message.senderType == 'user';
                    
                    return Align(
                      alignment: isFromUser 
                          ? Alignment.centerRight 
                          : Alignment.centerLeft,
                      child: Container(
                        margin: const EdgeInsets.symmetric(
                          horizontal: 12, 
                          vertical: 4
                        ),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: isFromUser 
                              ? CustomColors.primaryColor 
                              : Colors.grey.shade200,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              message.content,
                              style: TextStyle(
                                color: isFromUser ? Colors.white : Colors.black,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  '${message.createdAt.hour.toString().padLeft(2, '0')}:${message.createdAt.minute.toString().padLeft(2, '0')}',
                                  style: TextStyle(
                                    fontSize: 10,
                                    color: isFromUser ? Colors.white70 : Colors.black54,
                                  ),
                                ),
                                const SizedBox(width: 4),
                                if (isFromUser)
                                  Icon(
                                    message.isRead ? Icons.done_all : Icons.done,
                                    size: 12,
                                    color: Colors.white70,
                                  ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
          
          // Message input
          Container(
            padding: const EdgeInsets.all(8),
            color: Colors.white,
            child: Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _messageController,
                    decoration: InputDecoration(
                      hintText: 'Ketik pesan...',
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(24),
                      ),
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 8,
                      ),
                    ),
                  ),
                ),
                IconButton(
                  icon: Icon(
                    Icons.send,
                    color: CustomColors.primaryColor,
                  ),
                  onPressed: _sendMessage,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
} 