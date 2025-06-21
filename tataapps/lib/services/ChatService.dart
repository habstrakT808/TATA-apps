import 'package:flutter/foundation.dart';
import 'package:cloud_firestore/cloud_firestore.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/models/ChatModel.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:TATA/sendApi/Server.dart';

class ChatService {
  final FirebaseFirestore _firestore = FirebaseFirestore.instance;
  
  // Stream untuk mendapatkan pesan real-time
  Stream<List<MessageModel>> getMessagesForChat(String chatId) {
    return _firestore
        .collection('messages')
        .where('chat_id', isEqualTo: chatId)
        .orderBy('created_at', descending: false)
        .snapshots()
        .map((snapshot) {
          return snapshot.docs
              .map((doc) => MessageModel.fromFirestore(doc))
              .toList();
        });
  }
  
  // Stream untuk mendapatkan daftar chat room
  Stream<List<ChatModel>> getChatRooms(String userId) {
    return _firestore
        .collection('chats')
        .where('user_id', isEqualTo: userId)
        .orderBy('updated_at', descending: true)
        .snapshots()
        .map((snapshot) {
          return snapshot.docs
              .map((doc) => ChatModel.fromFirestore(doc))
              .toList();
        });
  }
  
  // Mengirim pesan
  Future<void> sendMessage(String chatId, String content, String senderType) async {
    try {
      // Ambil ID pengguna dari UserPreferences
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']['id'] ?? '';
      
      // Buat data pesan
      final message = {
        'chat_id': chatId,
        'content': content,
        'sender_type': senderType,
        'is_read': false,
        'created_at': FieldValue.serverTimestamp(),
      };
      
      // Tambahkan ke Firestore
      await _firestore.collection('messages').add(message);
      
      // Update chat room dengan pesan terakhir
      await _firestore.collection('chats').doc(chatId).update({
        'last_message': content,
        'updated_at': FieldValue.serverTimestamp(),
        'unread_count': FieldValue.increment(1),
      });
      
      // Kirim notifikasi ke backend untuk diteruskan ke FCM
      await _sendMessageNotification(chatId, content);
      
    } catch (e) {
      debugPrint('Error sending message: $e');
      rethrow;
    }
  }
  
  // Membuat chat room baru
  Future<String> createChatRoom(String userId, String adminId, {String? orderReference}) async {
    try {
      final chatData = {
        'user_id': userId,
        'admin_id': adminId,
        'order_reference': orderReference,
        'created_at': FieldValue.serverTimestamp(),
        'updated_at': FieldValue.serverTimestamp(),
        'last_message': 'Chat dibuat',
        'unread_count': 0,
      };
      
      final docRef = await _firestore.collection('chats').add(chatData);
      
      return docRef.id;
    } catch (e) {
      debugPrint('Error creating chat room: $e');
      throw e;
    }
  }
  
  // Menandai pesan sebagai sudah dibaca
  Future<void> markMessagesAsRead(String chatId, String currentUserType) async {
    try {
      final batch = _firestore.batch();
      
      // Ambil semua pesan yang belum dibaca dan bukan dari pengguna saat ini
      final snapshot = await _firestore
          .collection('messages')
          .where('chat_id', isEqualTo: chatId)
          .where('is_read', isEqualTo: false)
          .where('sender_type', isNotEqualTo: currentUserType)
          .get();
      
      for (var doc in snapshot.docs) {
        batch.update(doc.reference, {'is_read': true});
      }
      
      // Reset unread count di chat room
      batch.update(_firestore.collection('chats').doc(chatId), {'unread_count': 0});
      
      await batch.commit();
    } catch (e) {
      debugPrint('Error marking messages as read: $e');
      rethrow;
    }
  }
  
  // Mengirim notifikasi pesan ke backend untuk diteruskan ke FCM
  Future<void> _sendMessageNotification(String chatId, String message) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) return;
      
      final response = await http.post(
        Server.urlLaravel('chat/send-notification'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': token,
        },
        body: jsonEncode({
          'chat_id': chatId,
          'message': message,
        }),
      );
      
      if (response.statusCode != 200) {
        debugPrint('Failed to send notification: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error sending notification: $e');
    }
  }
  
  // Mendapatkan atau membuat chat untuk pesanan
  Future<String> getOrCreateChatForOrder(String orderReference) async {
    try {
      final userData = await UserPreferences.getUser();
      final userId = userData?['user']['id'];
      
      if (userId == null) {
        throw Exception('User not authenticated');
      }
      
      // Cek apakah chat sudah ada
      final existingChat = await _firestore
          .collection('chats')
          .where('user_id', isEqualTo: userId)
          .where('order_reference', isEqualTo: orderReference)
          .limit(1)
          .get();
      
      if (existingChat.docs.isNotEmpty) {
        return existingChat.docs.first.id;
      }
      
      // Jika tidak ada, buat chat baru melalui API
      final token = await UserPreferences.getToken();
      final response = await http.post(
        Server.urlLaravel('chat/create-for-order'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': token!,
        },
        body: jsonEncode({
          'pesanan_uuid': orderReference,
        }),
      );
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success' && data['data'] != null) {
          // Ambil chat_uuid dari response
          final chatUuid = data['data']['uuid'];
          
          // Buat dokumen di Firestore
          final chatData = {
            'user_id': userId,
            'admin_id': data['data']['admin_id'],
            'order_reference': orderReference,
            'created_at': FieldValue.serverTimestamp(),
            'updated_at': FieldValue.serverTimestamp(),
            'last_message': 'Chat dibuat untuk pesanan',
            'unread_count': 0,
          };
          
          await _firestore.collection('chats').doc(chatUuid).set(chatData);
          
          // Tambahkan pesan selamat datang
          await _firestore.collection('messages').add({
            'chat_id': chatUuid,
            'content': "Halo! Admin TATA siap membantu Anda terkait pesanan #$orderReference",
            'sender_type': 'admin',
            'is_read': false,
            'created_at': FieldValue.serverTimestamp(),
          });
          
          return chatUuid;
        }
      }
      
      throw Exception('Failed to create chat for order');
    } catch (e) {
      debugPrint('Error getting or creating chat for order: $e');
      throw e;
    }
  }
} 