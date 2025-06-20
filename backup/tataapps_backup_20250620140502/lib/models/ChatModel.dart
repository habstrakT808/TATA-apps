import 'package:cloud_firestore/cloud_firestore.dart';
import 'dart:math';

class ChatMessage {
  final String id;
  final String text;
  final String sender;
  final String timestamp;
  final bool isRead;
  final String? imageUrl;
  final String? fileUrl;
  final String messageType;

  ChatMessage({
    required this.id,
    required this.text,
    required this.sender,
    required this.timestamp,
    this.isRead = false,
    this.imageUrl,
    this.fileUrl,
    this.messageType = 'text',
  });

  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    // Handle various field names that might be used
    String messageId = json['uuid'] ?? json['id'] ?? '';
    String messageText = json['message'] ?? json['text'] ?? '';
    String messageSender = json['sender_type'] ?? json['sender'] ?? '';
    String messageTimestamp = json['created_at'] ?? json['timestamp'] ?? DateTime.now().toIso8601String();
    bool messageIsRead = json['is_read'] ?? json['read'] ?? false;
    String? messageImageUrl = json['file_url'] ?? json['image_url'];
    String messageType = json['message_type'] ?? 'text';
    
    // Debug log
    print('Creating ChatMessage from JSON: ${json.toString().substring(0, min(100, json.toString().length))}...');
    
    return ChatMessage(
      id: messageId,
      text: messageText,
      sender: messageSender,
      timestamp: messageTimestamp,
      isRead: messageIsRead,
      imageUrl: messageImageUrl,
      fileUrl: messageImageUrl,
      messageType: messageType,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'uuid': id,
      'message': text,
      'sender_type': sender,
      'created_at': timestamp,
      'is_read': isRead,
      'file_url': fileUrl ?? imageUrl,
      'message_type': messageType,
    };
  }
}

class ProductChat {
  final String id;
  final String title;
  final String category;
  final String price;
  final String imageUrl;
  final String packageType;

  ProductChat({
    required this.id,
    required this.title,
    required this.category,
    required this.price,
    required this.imageUrl,
    required this.packageType,
  });

  factory ProductChat.fromJson(Map<String, dynamic> json) {
    return ProductChat(
      id: json['id_jasa'] ?? json['id'] ?? '',
      title: json['kategori'] ?? json['title'] ?? '',
      category: json['jenis_pesanan'] ?? json['category'] ?? '',
      price: json['harga_paket_jasa']?.toString() ?? json['price'] ?? '',
      imageUrl: json['gambar'] ?? json['imageUrl'] ?? '',
      packageType: json['kelas_jasa'] ?? json['packageType'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'category': category,
      'price': price,
      'imageUrl': imageUrl,
      'packageType': packageType,
    };
  }
}

class ChatRoom {
  final String id;
  final String pesananId;
  final ProductChat product;
  final String lastMessage;
  final String lastSender;
  final String lastTimestamp;
  final int unreadCount;

  ChatRoom({
    required this.id,
    required this.pesananId,
    required this.product,
    required this.lastMessage,
    required this.lastSender,
    required this.lastTimestamp,
    this.unreadCount = 0,
  });

  factory ChatRoom.fromJson(Map<String, dynamic> json) {
    return ChatRoom(
      id: json['id'] ?? '',
      pesananId: json['pesanan_uuid'] ?? json['pesanan_id'] ?? '',
      product: ProductChat.fromJson(json['product'] ?? {}),
      lastMessage: json['last_message'] ?? '',
      lastSender: json['last_sender'] ?? '',
      lastTimestamp: json['updated_at'] ?? DateTime.now().toIso8601String(),
      unreadCount: json['unread_count'] ?? 0,
    );
  }
}

class ChatModel {
  final String id;
  final String userId;
  final String adminId;
  final String? orderReference;
  final DateTime createdAt;
  final DateTime updatedAt;
  final String lastMessage;
  final int unreadCount;
  
  ChatModel({
    required this.id,
    required this.userId,
    required this.adminId,
    this.orderReference,
    required this.createdAt,
    required this.updatedAt,
    this.lastMessage = '',
    this.unreadCount = 0,
  });
  
  factory ChatModel.fromFirestore(DocumentSnapshot doc) {
    Map<String, dynamic> data = doc.data() as Map<String, dynamic>;
    return ChatModel(
      id: doc.id,
      userId: data['user_id'] ?? '',
      adminId: data['admin_id'] ?? '',
      orderReference: data['order_reference'],
      createdAt: (data['created_at'] as Timestamp).toDate(),
      updatedAt: (data['updated_at'] as Timestamp).toDate(),
      lastMessage: data['last_message'] ?? '',
      unreadCount: data['unread_count'] ?? 0,
    );
  }
  
  Map<String, dynamic> toFirestore() {
    return {
      'user_id': userId,
      'admin_id': adminId,
      'order_reference': orderReference,
      'created_at': Timestamp.fromDate(createdAt),
      'updated_at': Timestamp.fromDate(updatedAt),
      'last_message': lastMessage,
      'unread_count': unreadCount,
    };
  }
}

class MessageModel {
  final String id;
  final String chatId;
  final String content;
  final String senderType; // 'user' or 'admin'
  final bool isRead;
  final DateTime createdAt;
  
  MessageModel({
    required this.id,
    required this.chatId,
    required this.content,
    required this.senderType,
    this.isRead = false,
    required this.createdAt,
  });
  
  factory MessageModel.fromFirestore(DocumentSnapshot doc) {
    Map<String, dynamic> data = doc.data() as Map<String, dynamic>;
    return MessageModel(
      id: doc.id,
      chatId: data['chat_id'] ?? '',
      content: data['content'] ?? '',
      senderType: data['sender_type'] ?? 'user',
      isRead: data['is_read'] ?? false,
      createdAt: (data['created_at'] as Timestamp).toDate(),
    );
  }
  
  Map<String, dynamic> toFirestore() {
    return {
      'chat_id': chatId,
      'content': content,
      'sender_type': senderType,
      'is_read': isRead,
      'created_at': Timestamp.fromDate(createdAt),
    };
  }
} 