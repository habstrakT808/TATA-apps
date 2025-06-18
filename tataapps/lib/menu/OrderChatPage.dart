import 'dart:convert';
import 'package:TATA/helper/fcm_helper.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/sendApi/ChatService.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class OrderChatPage extends StatefulWidget {
  final String pesananUuid;
  final String? jasaId;
  final String? jasaTitle;
  final String? jasaCategory;
  final String? packageType;
  final String? price;

  const OrderChatPage({
    super.key,
    required this.pesananUuid,
    this.jasaId,
    this.jasaTitle,
    this.jasaCategory,
    this.packageType,
    this.price,
  });

  @override
  State<OrderChatPage> createState() => _OrderChatPageState();
}

class _OrderChatPageState extends State<OrderChatPage> {
  final TextEditingController _controller = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  List<ChatMessage> messages = [];
  bool isLoading = true;
  ProductChat? currentProduct;

  @override
  void initState() {
    super.initState();
    _initializeProduct();
    _loadChat();
    
    // Subscribe to FCM updates
    FCMHelper().chatMessageStream.listen(_handleIncomingFCMMessage);
  }

  void _initializeProduct() {
    // Jika data produk diberikan, langsung buat instance ProductChat
    if (widget.jasaId != null && widget.jasaTitle != null) {
      currentProduct = ProductChat(
        id: widget.jasaId!,
        title: widget.jasaTitle!,
        category: widget.jasaCategory ?? 'Jasa',
        price: widget.price ?? '0',
        imageUrl: '',
        packageType: widget.packageType ?? 'basic',
      );
    } else {
      // Jika tidak, coba ambil dari server
      _fetchProductDetails();
    }
  }

  Future<void> _fetchProductDetails() async {
    try {
      // Coba dapatkan detail jasa dari server
      final product = await ChatApiService.getProductDetails(widget.pesananUuid);
      if (product != null) {
        setState(() {
          currentProduct = product;
        });
      }
    } catch (e) {
      print('Error getting product details: $e');
    }
  }

  Future<void> _loadChat() async {
    setState(() {
      isLoading = true;
    });

    try {
      // Ambil pesan-pesan chat
      final chatMessages = await ChatApiService.getChatMessages(widget.pesananUuid);
      
      setState(() {
        messages = chatMessages;
        isLoading = false;
      });

      // Scroll ke bawah setelah memuat pesan
      _scrollToBottom();

      // Mark messages as read
      final unreadMessages = chatMessages
          .where((msg) => !msg.isRead && msg.sender == 'admin')
          .map((msg) => msg.id)
          .toList();
          
      if (unreadMessages.isNotEmpty) {
        await ChatApiService.markMessagesAsRead(widget.pesananUuid, unreadMessages);
      }
    } catch (e) {
      print("Error loading chat: $e");
      setState(() {
        isLoading = false;
      });
      // Tampilkan pesan template jika gagal memuat chat
      _loadTemplateMessage();
    }
  }

  void _loadTemplateMessage() {
    if (messages.isEmpty) {
      setState(() {
        messages.add(ChatMessage(
          id: '1',
          text: "Selamat! Pesanan Anda telah diterima. Admin akan segera menghubungi Anda untuk informasi lebih lanjut.",
          sender: 'admin',
          timestamp: DateTime.now().toIso8601String(),
          isRead: true,
        ));
      });
    }
  }

  void _handleIncomingFCMMessage(Map<String, dynamic> messageData) {
    // Handle incoming FCM message
    if (messageData['pesanan_uuid'] == widget.pesananUuid) {
      final newMessage = ChatMessage(
        id: messageData['message_id'] ?? DateTime.now().millisecondsSinceEpoch.toString(),
        text: messageData['body'] ?? '',
        sender: 'admin',
        timestamp: DateTime.now().toIso8601String(),
        isRead: false,
      );
      
      setState(() {
        messages.add(newMessage);
      });
      
      _scrollToBottom();
    }
  }

  void _scrollToBottom() {
    if (_scrollController.hasClients) {
      Future.delayed(Duration(milliseconds: 300), () {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: Duration(milliseconds: 300),
          curve: Curves.easeOut,
        );
      });
    }
  }

  Future<void> sendMessage(String text) async {
    if (text.trim().isEmpty) return;

    _controller.clear();
    final newMessage = ChatMessage(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      text: text,
      sender: 'user',
      timestamp: DateTime.now().toIso8601String(),
    );
    
    setState(() {
      messages.add(newMessage);
    });

    // Scroll ke bawah
    _scrollToBottom();

    // Kirim pesan ke server
    final success = await ChatApiService.sendMessage(widget.pesananUuid, text);
    if (!success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Gagal mengirim pesan, coba lagi nanti")),
      );
    }
  }

  Widget buildProductCard() {
    if (currentProduct == null) return SizedBox();
    
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [BoxShadow(blurRadius: 4, color: Colors.black12)],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text("Kamu bertanya tentang produk ini",
              style: TextStyle(fontSize: 12, color: Colors.grey)),
          SizedBox(height: 8),
          Row(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: Container(
                  width: 60,
                  height: 60,
                  color: Colors.grey.shade300,
                  child: currentProduct!.category == 'logo' 
                      ? Icon(Icons.design_services, color: Colors.grey.shade800, size: 30)
                      : currentProduct!.category == 'banner'
                          ? Icon(Icons.image, color: Colors.grey.shade800, size: 30)
                          : Icon(Icons.work, color: Colors.grey.shade800, size: 30),
                ),
              ),
              SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('${currentProduct!.title}',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                    Text(currentProduct!.packageType,
                        style: TextStyle(fontSize: 12, color: Colors.grey)),
                    Text('Rp${currentProduct!.price}',
                        style:
                            TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget buildMessageBubble(ChatMessage message) {
    bool isUser = message.sender == 'user';
    
    // Format timestamp
    String formattedTime;
    try {
      final dateTime = DateTime.parse(message.timestamp);
      formattedTime = DateFormat('HH:mm').format(dateTime);
    } catch (e) {
      formattedTime = '';
    }
    
    return Align(
      alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: isUser
            ? EdgeInsets.only(top: 4, bottom: 4, left: 60, right: 16)
            : EdgeInsets.only(top: 4, bottom: 4, right: 60, left: 16),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: isUser ? Colors.green : Colors.grey.shade300,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              message.text,
              style: TextStyle(color: isUser ? Colors.white : Colors.black87),
            ),
            SizedBox(height: 2),
            Align(
              alignment: Alignment.bottomRight,
              child: Text(
                formattedTime,
                style: TextStyle(
                  fontSize: 10,
                  color: isUser ? Colors.white70 : Colors.grey.shade600,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Chat Pesanan'),
        actions: [
          IconButton(
            icon: Icon(Icons.refresh),
            onPressed: _loadChat,
          ),
        ],
      ),
      body: Stack(
        children: [
          Positioned(
            child: Align(
              alignment: Alignment.topLeft,
              child: Image.asset(
                Server.UrlGambar("atributhomecircle.png"),
              ),
            ),
          ),
          Positioned(
            bottom: 0,
            right: 0,
            child: Align(
              alignment: Alignment.bottomLeft,
              child: Image.asset(
                Server.UrlGambar("atributhomebigcircle.png"),
              ),
            ),
          ),
          Column(
            children: [
              // Product card
              if (currentProduct != null) buildProductCard(),
              
              // Chat messages
              Expanded(
                child: isLoading
                    ? Center(child: CircularProgressIndicator())
                    : ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.only(bottom: 16),
                        itemCount: messages.length,
                        itemBuilder: (context, index) {
                          final msg = messages[index];
                          return buildMessageBubble(msg);
                        },
                      ),
              ),
              
              // Chat input
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                decoration: BoxDecoration(
                  color: Colors.white,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 4,
                      offset: Offset(0, -2),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Scrollbar(
                        child: TextField(
                          controller: _controller,
                          keyboardType: TextInputType.multiline,
                          maxLines: 4,
                          minLines: 1,
                          scrollPhysics: BouncingScrollPhysics(),
                          decoration: const InputDecoration(
                            hintText: 'Ketik pesan...',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.all(Radius.circular(20)),
                            ),
                            filled: true,
                            fillColor: Colors.white,
                            contentPadding: EdgeInsets.symmetric(
                                horizontal: 16, vertical: 12),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    CircleAvatar(
                      backgroundColor: Colors.green,
                      child: IconButton(
                        icon: const Icon(Icons.send, color: Colors.white),
                        onPressed: () => sendMessage(_controller.text),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
} 