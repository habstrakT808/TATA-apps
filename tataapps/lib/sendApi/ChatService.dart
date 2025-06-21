import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:TATA/helper/user_preferences.dart';
import 'package:TATA/models/ChatModel.dart';
import 'package:TATA/sendApi/Server.dart';
import 'package:http/http.dart' as http;

class ChatService {
  // Mengambil daftar chat
  static Future<List<ChatModel>> getChatList() async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/chat/list'),
        headers: {
          'Accept': 'application/json',
          'Authorization': token,
        },
      );
      
      debugPrint('Get chat list status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final List<dynamic> chats = data['data'];
        
        return chats.map((chat) => ChatModel.fromJson(chat)).toList();
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal mengambil daftar chat: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error getting chat list: $e');
      rethrow;
    }
  }
  
  // Mengambil detail chat berdasarkan ID
  static Future<Map<String, dynamic>> getChatDetail(String chatId) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/chat/messages?chat_uuid=$chatId'),
        headers: {
          'Accept': 'application/json',
          'Authorization': token,
        },
      );
      
      debugPrint('Get chat detail status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data;
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal mengambil detail chat: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error getting chat detail: $e');
      rethrow;
    }
  }
  
  // Mengirim pesan
  static Future<Map<String, dynamic>> sendMessage(String chatId, String message) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/send'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': token,
        },
        body: jsonEncode({
          'chat_uuid': chatId,
          'message': message,
          'message_type': 'text',
        }),
      );
      
      debugPrint('Send message status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data;
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal mengirim pesan: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error sending message: $e');
      rethrow;
    }
  }
  
  // Membuat chat untuk pesanan
  static Future<Map<String, dynamic>> createChatForOrder(String pesananUuid) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/create-for-order'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': token,
        },
        body: jsonEncode({
          'pesanan_uuid': pesananUuid,
        }),
      );
      
      debugPrint('Create chat for order status: ${response.statusCode}');
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        return data;
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal membuat chat: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error creating chat for order: $e');
      rethrow;
    }
  }
  
  // Menandai pesan sebagai sudah dibaca
  static Future<Map<String, dynamic>> markMessagesAsRead(String chatId) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.post(
        Server.urlLaravel('mobile/chat/mark-read'),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': token,
        },
        body: jsonEncode({
          'chat_uuid': chatId,
        }),
      );
      
      debugPrint('Mark messages as read status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data;
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal menandai pesan sebagai sudah dibaca: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error marking messages as read: $e');
      rethrow;
    }
  }
  
  // Mengambil pesan berdasarkan ID pesanan
  static Future<Map<String, dynamic>> getMessagesByPesanan(String pesananUuid) async {
    try {
      final token = await UserPreferences.getToken();
      if (token == null) {
        throw Exception('Token tidak ditemukan');
      }
      
      final response = await http.get(
        Server.urlLaravel('mobile/chat/messages-by-pesanan/$pesananUuid'),
        headers: {
          'Accept': 'application/json',
          'Authorization': token,
        },
      );
      
      debugPrint('Get messages by pesanan status: ${response.statusCode}');
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data;
      } else if (response.statusCode == 401) {
        // Token tidak valid, hapus token
        await UserPreferences.clearToken();
        throw Exception('Sesi telah berakhir, silahkan login kembali');
      } else {
        throw Exception('Gagal mengambil pesan: ${response.body}');
      }
    } catch (e) {
      debugPrint('Error getting messages by pesanan: $e');
      rethrow;
    }
  }
} 