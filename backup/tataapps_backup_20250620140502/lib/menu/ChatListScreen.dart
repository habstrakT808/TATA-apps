import 'package:flutter/material.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/services/ChatService.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/menu/ChatDetailScreen.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:intl/intl.dart';

class ChatListScreen extends StatefulWidget {
  const ChatListScreen({Key? key}) : super(key: key);
  
  @override
  _ChatListScreenState createState() => _ChatListScreenState();
}

class _ChatListScreenState extends State<ChatListScreen> {
  final ChatService _chatService = ChatService();
  String? _userId;
  bool _isLoading = true;
  
  @override
  void initState() {
    super.initState();
    _loadUserId();
  }
  
  Future<void> _loadUserId() async {
    try {
      final userData = await UserPreferences.getUser();
      if (userData != null) {
        setState(() {
          _userId = userData['user']['id'].toString();
          _isLoading = false;
        });
      } else {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      print('Error loading user data: $e');
      setState(() {
        _isLoading = false;
      });
    }
  }
  
  String _formatDate(DateTime date) {
    final today = DateTime.now();
    
    if (date.year == today.year && date.month == today.month && date.day == today.day) {
      return DateFormat('HH:mm').format(date);
    } else if (date.year == today.year) {
      return DateFormat('dd MMM').format(date);
    } else {
      return DateFormat('dd/MM/yy').format(date);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Pesan'),
          backgroundColor: CustomColors.primaryColor,
        ),
        body: const Center(child: CircularProgressIndicator()),
      );
    }
    
    if (_userId == null) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Pesan'),
          backgroundColor: CustomColors.primaryColor,
        ),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Text('Anda perlu login untuk melihat pesan'),
              const SizedBox(height: 16),
              ElevatedButton(
                onPressed: () {
                  // Navigate to login screen
                },
                child: const Text('Login'),
              ),
            ],
          ),
        ),
      );
    }
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Chat'),
        backgroundColor: CustomColors.primaryColor,
      ),
      body: StreamBuilder<List<ChatModel>>(
        stream: _chatService.getChatsForUser(_userId!),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          
          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Image.asset(
                    'assets/images/empty_chat.png',
                    width: 120,
                    height: 120,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) => 
                      Icon(Icons.chat_bubble_outline, size: 80, color: Colors.grey[300]),
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada percakapan',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey[600],
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Mulai percakapan dari halaman detail pesanan',
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey,
                    ),
                  ),
                ],
              ),
            );
          }
          
          final chats = snapshot.data!;
          
          return ListView.builder(
            itemCount: chats.length,
            itemBuilder: (context, index) {
              final chat = chats[index];
              final hasUnread = chat.unreadCount > 0;
              
              return Card(
                margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                child: ListTile(
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 16, 
                    vertical: 8,
                  ),
                  leading: CircleAvatar(
                    backgroundColor: CustomColors.primaryColor,
                    child: const Icon(Icons.support_agent, color: Colors.white),
                  ),
                  title: Row(
                    children: [
                      Expanded(
                        child: Text(
                          'Admin TATA',
                          style: TextStyle(
                            fontWeight: hasUnread ? FontWeight.bold : FontWeight.normal,
                          ),
                        ),
                      ),
                      Text(
                        _formatDate(chat.updatedAt),
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                  subtitle: Row(
                    children: [
                      Expanded(
                        child: chat.orderReference != null && chat.orderReference!.isNotEmpty
                          ? Text(
                              chat.lastMessage.isNotEmpty 
                                ? chat.lastMessage
                                : 'Pesanan #${chat.orderReference}',
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                fontWeight: hasUnread ? FontWeight.bold : FontWeight.normal,
                              ),
                            )
                          : Text(
                              chat.lastMessage.isNotEmpty
                                ? chat.lastMessage
                                : 'Mulai percakapan',
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                      ),
                      if (hasUnread)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: CustomColors.primaryColor,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            chat.unreadCount.toString(),
                            style: const TextStyle(
                              fontSize: 12,
                              color: Colors.white,
                            ),
                          ),
                        ),
                    ],
                  ),
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ChatDetailScreen(chatId: chat.id),
                      ),
                    );
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
} 