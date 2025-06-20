import 'package:flutter/material.dart';
import 'package:firebase_auth/firebase_auth.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/services/ChatService.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/src/CustomColors.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:intl/intl.dart';

class ChatDetailScreen extends StatefulWidget {
  final String chatId;
  
  const ChatDetailScreen({required this.chatId, Key? key}) : super(key: key);
  
  @override
  _ChatDetailScreenState createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends State<ChatDetailScreen> {
  final ChatService _chatService = ChatService();
  final TextEditingController _messageController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  String? _userId;
  ChatModel? _chatModel;
  bool _isLoading = true;
  String _adminName = "Admin Chat"; // Default admin name
  String _orderReference = ""; // Order reference
  String _kategori = "Logo"; // Default kategori
  
  // Data pesanan untuk ditampilkan di product card
  Map<String, dynamic> _pesananData = {
    'judul': 'Desain',
    'kategori': 'Design',
    'kelas_jasa': 'Standard',
    'harga_paket_jasa': 0,
    'deskripsi': '',
    'gambar_referensi': '',
  };
  
  // Tambahkan variabel untuk menyimpan pesan lokal
  List<MessageModel> _localMessages = [];
  bool _isFirstLoad = true;
  int _previousMessageCount = 0;
  
  @override
  void initState() {
    super.initState();
    try {
    _loadUserId();
    _loadChatData();
    _markMessagesAsRead();
      
      // Inisialisasi Firestore
      _chatService.initFirestore().then((_) {
        // Tambahkan debug info setelah inisialisasi
        _chatService.printDebugInfo(widget.chatId);
      }).catchError((error) {
        print('Error initializing Firestore: $error');
      });
    } catch (e) {
      print('Error in initState: $e');
    }
  }
  
  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    super.dispose();
  }
  
  Future<void> _loadUserId() async {
    final userData = await UserPreferences.getUser();
    setState(() {
      _userId = userData?['user']['id'];
    });
  }
  
  Future<void> _loadChatData() async {
    try {
      // Dapatkan dokumen chat
      final chatDoc = await FirebaseFirestore.instance
          .collection('chats')
          .doc(widget.chatId)
          .get();
      
      if (chatDoc.exists) {
        final chatData = chatDoc.data() as Map<String, dynamic>;
        final chatModel = ChatModel.fromFirestore(chatDoc);
        
        // Verifikasi order reference
        final orderRef = chatModel.orderReference;
        if (orderRef == null || orderRef.isEmpty) {
          print('Warning: Chat ${widget.chatId} tidak memiliki order reference yang valid');
        } else {
          print('Chat ${widget.chatId} memiliki order reference: $orderRef');
        }
        
        // Dapatkan data pesanan berdasarkan order reference
        final pesananData = await ChatService().getPesananData(orderRef ?? '');
        
        // Debug data pesanan
        print('Data pesanan yang didapat: $pesananData');
        
        // Simpan kategori ke dalam dokumen chat untuk referensi masa depan
        if (pesananData['kategori'] != null && pesananData['kategori'].toString().isNotEmpty) {
          try {
            await FirebaseFirestore.instance.collection('chats').doc(widget.chatId).update({
              'kategori': pesananData['kategori']
            });
            print('Kategori ${pesananData['kategori']} disimpan ke dokumen chat');
          } catch (e) {
            print('Error menyimpan kategori ke dokumen chat: $e');
          }
        }
        
        // Update state dengan data yang didapat
        setState(() {
          _chatModel = chatModel;
          _pesananData = pesananData;
          _isLoading = false;
          
          // Set order reference dan admin name
          _orderReference = orderRef ?? '';
          _adminName = pesananData['admin_name'] ?? 'Admin Chat';
          
          // Set kategori
          print('Order reference: $_orderReference');
          print('Admin name: $_adminName ');
          print('Kategori: ${pesananData['kategori']}');
          
          // Gunakan kategori dari data pesanan
          _kategori = pesananData['kategori'] ?? 'Logo';
          print('Menggunakan kategori dari data pesanan: $_kategori');
        });
        
        // Load pesan-pesan chat
        _loadMessages();
      } else {
        print('Chat document not found for ID: ${widget.chatId}');
        setState(() {
          _isLoading = false;
          _chatModel = ChatModel(
            id: widget.chatId,
            userId: '',
            adminId: '',
            orderReference: '',
            createdAt: DateTime.now(),
            updatedAt: DateTime.now(),
            lastMessage: 'Chat tidak ditemukan',
            unreadCount: 0
          );
        });
      }
    } catch (e) {
      print('Error loading chat data: $e');
      setState(() {
        _isLoading = false;
        _chatModel = ChatModel(
          id: widget.chatId,
          userId: '',
          adminId: '',
          orderReference: '',
          createdAt: DateTime.now(),
          updatedAt: DateTime.now(),
          lastMessage: 'Error: $e',
          unreadCount: 0
        );
      });
    }
  }
  
  // Helper method to capitalize string
  String _capitalize(String s) {
    if (s == null || s.isEmpty) {
      return '';
    }
    return s[0].toUpperCase() + s.substring(1).toLowerCase();
  }
  
  // Helper method to get default price based on category
  int _getDefaultHarga(String kategori) {
    if (kategori.toLowerCase().contains('poster')) {
      return 300000;
    } else if (kategori.toLowerCase().contains('banner')) {
      return 200000;
    } else {
      return 100000; // Default for Logo
    }
  }
  
  void _markMessagesAsRead() {
    if (widget.chatId.isNotEmpty) {
      _chatService.markMessagesAsRead(widget.chatId, 'user');
    }
  }
  
  void _sendMessage() async {
    if (_messageController.text.trim().isNotEmpty) {
      final messageText = _messageController.text.trim();
      _messageController.clear();
      
      // Buat pesan lokal dan tambahkan ke daftar pesan lokal
      final localMessage = MessageModel(
        id: 'local_${DateTime.now().millisecondsSinceEpoch}',
        chatId: widget.chatId,
        content: messageText,
        senderId: 'user',
        senderType: 'user',
        timestamp: DateTime.now(),
        isRead: false,
      );
      
      // Update UI dengan optimistic update
      setState(() {
        _localMessages.add(localMessage);
      });
      
      // Scroll ke pesan terbaru
      _scrollToBottom();
      
      // Kirim pesan ke Firestore
      final result = await _chatService.sendMessage(
        widget.chatId,
        messageText,
        'user'
      );
      
      if (!result) {
        // Jika gagal, hapus pesan lokal dan tampilkan snackbar error
        setState(() {
          _localMessages.removeWhere((msg) => msg.id == localMessage.id);
        });
        
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal mengirim pesan. Coba lagi.'),
            backgroundColor: Colors.red,
            action: SnackBarAction(
              label: 'Coba Lagi',
              textColor: Colors.white,
              onPressed: () {
                _messageController.text = messageText;
              },
            ),
          ),
        );
      }
    }
  }
  
  // Fungsi untuk scroll ke pesan terbaru
  void _scrollToBottom() {
    try {
      if (_scrollController.hasClients) {
        Future.delayed(Duration(milliseconds: 100), () {
          try {
            _scrollController.animateTo(
              _scrollController.position.maxScrollExtent,
              duration: Duration(milliseconds: 300),
              curve: Curves.easeOut,
            );
          } catch (e) {
            print('Error scrolling to bottom: $e');
          }
        });
      }
    } catch (e) {
      print('Error in _scrollToBottom: $e');
    }
  }
  
  // Menampilkan gambar pesanan 
  Widget _getOrderImage(pesananData) {
    // Debug data
    print("Getting order image for pesanan data: ${pesananData?.toString()}");
    
    if (pesananData == null) {
      print("Pesanan data is null, using default image");
      return _getDefaultImageContainer();
    }
    
    // Ensure kategori is properly set
    String kategori = "Logo"; // Default
    if (pesananData['kategori'] != null && pesananData['kategori'].toString().isNotEmpty) {
      kategori = pesananData['kategori'].toString();
    }
    
    // Cek apakah ada gambar referensi
    if (pesananData['gambar_referensi'] != null && 
        pesananData['gambar_referensi'].toString().isNotEmpty &&
        pesananData['gambar_referensi'].toString() != "null") {
      print("Menggunakan gambar referensi: ${pesananData['gambar_referensi']}");
      
      // Validate URL
      String imageUrl = pesananData['gambar_referensi'].toString();
      if (!imageUrl.startsWith('http')) {
        imageUrl = 'http://localhost:8000/${imageUrl.replaceFirst('/', '')}';
      }
      
      return Container(
        height: 100,
        width: 100,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.grey[300]!),
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(8),
          child: Image.network(
            imageUrl,
            fit: BoxFit.cover,
            errorBuilder: (context, error, stackTrace) {
              print("Error loading image: $error");
              return _getCategoryImage(kategori);
            },
            loadingBuilder: (context, child, loadingProgress) {
              if (loadingProgress == null) return child;
              return Center(child: CircularProgressIndicator());
            },
          ),
        ),
      );
    } else {
      print("Tidak ada gambar referensi, menggunakan gambar kategori untuk $kategori");
      return _getCategoryImage(kategori);
    }
  }
  
  // Get image based on category
  Widget _getCategoryImage(String kategori) {
    print("Getting image for category: ${kategori.toLowerCase()}");
    
    String assetPath;
    if (kategori.toLowerCase().contains('poster')) {
      assetPath = 'assets/images/poster_placeholder.png';
    } else if (kategori.toLowerCase().contains('banner')) {
      assetPath = 'assets/images/banner_placeholder.png';
    } else {
      assetPath = 'assets/images/logo_placeholder.png';
    }
    
    return Container(
      height: 100,
      width: 100,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.grey[300]!),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(8),
        child: Image.asset(
          assetPath,
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) {
            // If asset not found, use colored container
            return _getDefaultImageContainer();
          },
        ),
      ),
    );
  }
  
  // Default colored container as fallback
  Widget _getDefaultImageContainer() {
    return Container(
      height: 100,
      width: 100,
      decoration: BoxDecoration(
        color: CustomColors.primaryColor.withOpacity(0.2),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.grey[300]!),
      ),
      child: Center(
        child: Icon(
          Icons.image,
          color: CustomColors.primaryColor,
          size: 40,
        ),
      ),
    );
  }
  
  // Format price
  String _formatPrice(dynamic price) {
    if (price == null) return '0';
    
    var formatter = RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))');
    
    if (price is String) {
      try {
        int numericPrice = int.parse(price);
        return numericPrice.toString().replaceAllMapped(formatter, (Match m) => '${m[1]}.');
      } catch (e) {
        return '0';
      }
    } else if (price is int) {
      return price.toString().replaceAllMapped(formatter, (Match m) => '${m[1]}.');
    }
    
    return '0';
  }
  
  // Widget untuk menampilkan produk
  Widget _buildProductCard() {
    // Menggunakan data pesanan dari state
    return Container(
      margin: EdgeInsets.symmetric(horizontal: 8.0, vertical: 10.0),
      padding: EdgeInsets.all(12.0),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15.0),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            spreadRadius: 1,
            blurRadius: 3,
            offset: Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(10.0),
            child: _getOrderImage(_pesananData),
          ),
          SizedBox(width: 12.0),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _pesananData?['judul'] ?? 'Desain Logo',
                  style: TextStyle(
                    fontSize: 16.0,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                SizedBox(height: 4.0),
                Text(
                  _pesananData?['kelas_jasa'] ?? 'Standard',
                  style: TextStyle(
                    fontSize: 14.0,
                    color: Colors.grey[700],
                  ),
                ),
                SizedBox(height: 4.0),
                Text(
                  'Rp ${_formatPrice(_pesananData?['harga_paket_jasa'])}',
                  style: TextStyle(
                    fontSize: 14.0,
                    fontWeight: FontWeight.bold,
                    color: CustomColors.primaryColor,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
  
  Future<void> _loadMessages() async {
    try {
      print('Loading messages for chat ${widget.chatId}');
      
      // Verifikasi order reference
      if (_orderReference.isEmpty) {
        print('Warning: Order reference is empty, trying to get from chat model');
        _orderReference = _chatModel?.orderReference ?? '';
        if (_orderReference.isEmpty) {
          print('Error: No valid order reference found');
          return;
        }
      }
      
      // Load messages from Firestore
      final messages = await _chatService.loadMessages(_orderReference);
      print('Loaded ${messages.length} messages for chat ${widget.chatId}');
      
      // Update state with messages
      if (mounted) {
        setState(() {
          _localMessages = messages.map((m) => MessageModel(
            id: m.id,
            chatId: widget.chatId,
            content: m.text,
            senderId: m.sender == 'admin' ? 'admin' : _userId ?? 'user',
            senderType: m.sender,
            timestamp: DateTime.parse(m.timestamp),
            isRead: m.isRead,
          )).toList();
          
          // Check if we need to scroll to bottom
          if (_isFirstLoad || _localMessages.length > _previousMessageCount) {
            _isFirstLoad = false;
            _previousMessageCount = _localMessages.length;
            
            // Schedule scroll after build
            WidgetsBinding.instance.addPostFrameCallback((_) {
              if (_scrollController.hasClients) {
                _scrollController.animateTo(
                  _scrollController.position.maxScrollExtent,
                  duration: Duration(milliseconds: 300),
                  curve: Curves.easeOut,
                );
              }
            });
          }
        });
      }
    } catch (e) {
      print('Error loading messages: $e');
    }
  }
  
  // Helper method to convert between MessageModel types
  MessageModel _convertMessageModel(dynamic firestoreMessage) {
    // If it's already our local MessageModel type, just return it
    if (firestoreMessage is MessageModel) {
      return firestoreMessage;
    }
    
    // Otherwise, convert from ChatModel.MessageModel to our local MessageModel
    return MessageModel(
      id: firestoreMessage.id,
      chatId: firestoreMessage.chatId,
      content: firestoreMessage.content,
      senderId: firestoreMessage.senderType == 'admin' ? 'admin' : _userId ?? 'user',
      senderType: firestoreMessage.senderType,
      timestamp: firestoreMessage.createdAt,
      isRead: firestoreMessage.isRead,
    );
  }
  
  @override
  Widget build(BuildContext context) {
    // Set admin name from chat model if available
    final adminName = _adminName;
    
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: const Text('Chat dengan Admin'),
        backgroundColor: CustomColors.primaryColor,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Product info if available (based on Figma design)
          if (_chatModel?.orderReference != null && _chatModel!.orderReference!.isNotEmpty)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    offset: const Offset(0, 2),
                    blurRadius: 6,
                  )
                ],
              ),
              child: Row(
                children: [
                  Container(
                    height: 50,
                    width: 50,
                    decoration: BoxDecoration(
                      color: CustomColors.primaryColor,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: const Center(
                    child: Icon(
                      Icons.shopping_bag,
                        color: Colors.white,
                        size: 24,
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          'Pesanan #${_chatModel!.orderReference}',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
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
            child: Container(
              decoration: BoxDecoration(
                // Latar belakang chat dengan gradasi hijau muda
                gradient: LinearGradient(
                  begin: Alignment.topRight,
                  end: Alignment.bottomLeft,
                  colors: [
                    Colors.green.withOpacity(0.05),
                    Colors.white,
                  ],
                ),
              ),
            child: StreamBuilder<List<dynamic>>(
              stream: _chatService.getMessagesForChat(widget.chatId),
              builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting && _isFirstLoad) {
                  return const Center(child: CircularProgressIndicator());
                }
                
                  // Gabungkan pesan dari Firestore dengan pesan lokal
                  List<MessageModel> messages = [];
                  
                  if (snapshot.hasData) {
                    // Convert the messages from Firestore to our local MessageModel
                    for (var firestoreMessage in snapshot.data!) {
                      messages.add(_convertMessageModel(firestoreMessage));
                    }
                    
                    _isFirstLoad = false;
                    
                    // Hapus pesan lokal yang sudah ada di Firestore
                    for (var message in messages) {
                      _localMessages.removeWhere((localMsg) => 
                        localMsg.content == message.content && 
                        localMsg.senderType == message.senderType &&
                        (message.createdAt.difference(localMsg.createdAt).inSeconds.abs() < 10)
                      );
                    }
                    
                    // Periksa apakah ada pesan baru, jika ya scroll ke bawah
                    if (messages.length > _previousMessageCount) {
                      _previousMessageCount = messages.length;
                      _scrollToBottom();
                    }
                  }
                  
                  // Tambahkan pesan lokal yang tersisa
                  messages.addAll(_localMessages);
                  
                  // Urutkan berdasarkan waktu
                  messages.sort((a, b) => a.createdAt.compareTo(b.createdAt));
                  
                  if (messages.isEmpty) {
                  return const Center(child: Text('Belum ada pesan'));
                }
                
                  // Scroll ke bawah setelah render
                  WidgetsBinding.instance.addPostFrameCallback((_) {
                    if (_localMessages.isNotEmpty || messages.length != _previousMessageCount) {
                      _scrollToBottom();
                      _previousMessageCount = messages.length;
                    }
                  });
                
                return ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
                  itemCount: messages.length,
                  itemBuilder: (context, index) {
                    final message = messages[index];
                    final isFromUser = message.senderType == 'user';
                      final isFirstMessage = index == 0;
                      
                      // Tampilkan header dengan nama admin pada pesan pertama jika dari admin
                      if (isFirstMessage && !isFromUser) {
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              margin: const EdgeInsets.only(bottom: 8),
                              child: Text(
                                "Hai! $adminName siap membantu Anda terkait pesanan #${_chatModel?.orderReference ?? ''}",
                              style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ),
                            // Tampilkan product card di awal chat
                            _buildProductCard(),
                            const SizedBox(height: 8),
                            _buildMessageBubble(message, isFromUser),
                          ],
                        );
                      }
                      
                      // Handle pesan khusus dengan keyword tertentu
                      if (!isFromUser && (
                          message.content.contains("produk ini") || 
                          message.content.contains("pertanyaan tentang produk") || 
                          message.content.contains("desain poster") ||
                          message.content.contains("Desain Poster")
                        )) {
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                            _buildMessageBubble(message, isFromUser),
                            const SizedBox(height: 12),
                            _buildProductCard(),
                          ],
                        );
                      }
                      
                      return _buildMessageBubble(message, isFromUser);
                  },
                );
              },
              ),
            ),
          ),
          
          // Message input - Modern design based on Figma
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
            color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  offset: const Offset(0, -2),
                  blurRadius: 6,
                )
              ],
            ),
            child: Row(
              children: [
                Expanded(
                  child: Container(
                    height: 48,
                    decoration: BoxDecoration(
                      color: Colors.grey.shade100,
                      borderRadius: BorderRadius.circular(24),
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    child: Row(
                      children: [
                        const SizedBox(width: 12),
                Expanded(
                  child: TextField(
                    controller: _messageController,
                            decoration: const InputDecoration(
                      hintText: 'Ketik pesan...',
                              border: InputBorder.none,
                              hintStyle: TextStyle(color: Colors.grey),
                              contentPadding: EdgeInsets.zero,
                            ),
                            textAlignVertical: TextAlignVertical.center,
                          ),
                        ),
                        Container(
                          decoration: BoxDecoration(
                            color: CustomColors.primaryColor,
                            shape: BoxShape.circle,
                          ),
                          child: IconButton(
                            icon: const Icon(Icons.arrow_forward, color: Colors.white),
                            onPressed: _sendMessage,
                            constraints: const BoxConstraints.tightFor(width: 40, height: 40),
                            padding: EdgeInsets.zero,
                            iconSize: 20,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
  
  // Builder method for message bubble
  Widget _buildMessageBubble(MessageModel message, bool isFromUser) {
    return Align(
      alignment: isFromUser ? Alignment.centerRight : Alignment.centerLeft,
      child: ConstrainedBox(
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.75,
        ),
        child: Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            color: isFromUser 
                ? CustomColors.primaryColor 
                : Colors.grey.shade100,
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(isFromUser ? 16 : 4),
              topRight: Radius.circular(isFromUser ? 4 : 16),
              bottomLeft: const Radius.circular(16),
              bottomRight: const Radius.circular(16),
            ),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 3,
                offset: const Offset(0, 1),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                message.content,
                style: TextStyle(
                  color: isFromUser ? Colors.white : Colors.black87,
                  fontSize: 14,
                ),
              ),
              const SizedBox(height: 5),
              Align(
                alignment: Alignment.bottomRight,
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      '${message.createdAt.hour.toString().padLeft(2, '0')}:${message.createdAt.minute.toString().padLeft(2, '0')}',
                      style: TextStyle(
                        fontSize: 10,
                        color: isFromUser ? Colors.white70 : Colors.black38,
                      ),
                    ),
                    const SizedBox(width: 4),
                    if (isFromUser)
                      Icon(
                        message.id.startsWith('local_') 
                            ? Icons.access_time_outlined 
                            : (message.isRead ? Icons.done_all : Icons.done),
                        size: 12,
                        color: message.isRead ? Colors.white : Colors.white70,
                      ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Message model for local use
class MessageModel {
  final String id;
  final String chatId;
  final String content;
  final String senderId;
  final String senderType;
  final DateTime createdAt;
  final bool isRead;
  
  MessageModel({
    required this.id,
    required this.chatId,
    required this.content,
    required this.senderId,
    required this.senderType,
    required DateTime timestamp,
    this.isRead = false,
  }) : createdAt = timestamp;
} 